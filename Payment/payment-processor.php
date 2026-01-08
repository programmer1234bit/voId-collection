<?php

require_once '../config.php';

class PaymentProcessor {
    private $cachedToken = null;
    private $tokenExpiry = 0;
    private $clientId = '';
    private $clientSecret = '';

    public function __construct() {
        $this->clientId = FLW_CLIENT_ID;
        $this->clientSecret = FLW_CLIENT_SECRET;
    }

    /**
     * Get OAuth 2.0 Access Token from Flutterwave
     */
    private function getAccessToken() {
        // Check if token is still valid
        if ($this->cachedToken && time() < $this->tokenExpiry) {
            return $this->cachedToken;
        }

        error_log("📥 Requesting new access token from Flutterwave...");

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://idp.flutterwave.com/realms/flutterwave/protocol/openid-connect/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("❌ Token Error (HTTP $httpCode): " . $response);
            throw new Exception("Failed to get access token: HTTP $httpCode");
        }

        $data = json_decode($response, true);
        
        if (!isset($data['access_token'])) {
            error_log("❌ No access token in response: " . json_encode($data));
            throw new Exception("No access token in response");
        }
        
        $this->cachedToken = $data['access_token'];
        $this->tokenExpiry = time() + ($data['expires_in'] - 60);

        error_log("✅ Access token obtained, expires in {$data['expires_in']} seconds");
        return $this->cachedToken;
    }

    /**
     * Create Payment Link
     */
    public function createPaymentLink($amount, $email, $name, $paymentMethod = 'mobile_money', $network = 'MTN', $cardType = 'visa') {
        error_log("\n=== PAYMENT CREATION START ===");
        error_log("📥 Request: amount=$amount, email=$email, name=$name, method=$paymentMethod, network=$network");

        // Validate required fields
        if (!$amount || !$email || !$name) {
            throw new Exception("Missing required fields: amount, email, name");
        }

        // Validate amount
        $parsedAmount = floatval($amount);
        if ($parsedAmount <= 0 || !is_numeric($amount)) {
            throw new Exception("Amount must be a positive number");
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        error_log("✅ Validation passed");

        // Get OAuth token
        try {
            $token = $this->getAccessToken();
            error_log("✅ Successfully obtained access token");
        } catch (Exception $e) {
            error_log("❌ Failed to get access token: " . $e->getMessage());
            throw new Exception("Authentication failed: " . $e->getMessage());
        }

        // Parse name
        $nameParts = array_filter(explode(' ', trim($name)));
        $firstName = $nameParts[0] ?? 'Customer';
        $lastName = implode(' ', array_slice($nameParts, 1)) ?: 'User';

        // Generate reference - alphanumeric only
        $timestamp = base_convert(time(), 10, 36);
        $random = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 9);
        $reference = "juma" . $timestamp . $random;

        error_log("📤 Creating charge via Orchestrator Flow...");
        error_log("Reference: $reference, Payment Method: $paymentMethod");

        // Network to country mapping
        $networkCountryMap = [
            'MTN' => ['countryCode' => '233', 'currency' => 'GHS', 'name' => 'Ghana'],
            'VODAFONE' => ['countryCode' => '233', 'currency' => 'GHS', 'name' => 'Ghana'],
            'AIRTEL' => ['countryCode' => '255', 'currency' => 'TZS', 'name' => 'Tanzania'],
            'TIGO' => ['countryCode' => '255', 'currency' => 'TZS', 'name' => 'Tanzania'],
            'MPESA' => ['countryCode' => '254', 'currency' => 'KES', 'name' => 'Kenya'],
            'HALOTEL' => ['countryCode' => '255', 'currency' => 'TZS', 'name' => 'Tanzania']
        ];

        $method = $paymentMethod ?: 'mobile_money';
        $selectedNetwork = strtoupper($network ?: 'MTN');
        $selectedCard = strtolower($cardType ?: 'visa');

        if ($method === 'mobile_money') {
            $countryInfo = $networkCountryMap[$selectedNetwork] ?? $networkCountryMap['MTN'];
            
            $orchestratorPayload = [
                'amount' => floatval($amount),
                'currency' => $countryInfo['currency'],
                'reference' => $reference,
                'customer' => [
                    'email' => $email,
                    'name' => [
                        'first' => $firstName,
                        'last' => $lastName
                    ],
                    'phone' => [
                        'country_code' => $countryInfo['countryCode'],
                        'number' => '9012345678'
                    ]
                ],
                'payment_method' => [
                    'type' => 'mobile_money',
                    'mobile_money' => [
                        'country_code' => $countryInfo['countryCode'],
                        'network' => $selectedNetwork,
                        'phone_number' => '9012345678'
                    ]
                ]
            ];

            error_log("Mobile Money: $selectedNetwork, Country: {$countryInfo['name']}, Currency: {$countryInfo['currency']}");
        } else {
            $orchestratorPayload = [
                'amount' => intval($amount),
                'currency' => 'NGN',
                'country' => 'NG',
                'reference' => $reference,
                'customer' => [
                    'email' => $email,
                    'name' => [
                        'first' => $firstName,
                        'last' => $lastName
                    ]
                ],
                'payment_method' => [
                    'type' => 'card',
                    'card' => [
                        'nonce' => 'n0Ad6mOzVnLI'
                    ]
                ]
            ];

            error_log("Card Type: $selectedCard");
        }

        error_log("📋 Payload: " . json_encode($orchestratorPayload, JSON_PRETTY_PRINT));

        // Make API call to Flutterwave
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://developersandbox-api.flutterwave.com/orchestration/direct-charges',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($orchestratorPayload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'X-Trace-Id: trace_' . time(),
                'X-Idempotency-Key: charge_' . time() . '_' . substr(str_shuffle('0123456789'), 0, 9),
                'X-Scenario-Key: ' . ($method === 'mobile_money' ? 'scenario:auth_redirect' : 'scenario:auth_3ds&issuer:approved')
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        error_log("📡 HTTP Code: $httpCode");
        error_log("📡 Raw Response: " . $response);

        if ($curlError) {
            error_log("❌ CURL Error: $curlError");
            throw new Exception("Network error: $curlError");
        }

        $chargeResponse = json_decode($response, true);
        
        error_log("📊 Decoded Response: " . json_encode($chargeResponse, JSON_PRETTY_PRINT));

        // Check if response is valid
        if (!$chargeResponse) {
            error_log("❌ Failed to decode JSON response");
            throw new Exception("Invalid JSON response from Flutterwave");
        }

        // Check for success status (200 or 201)
        if ($httpCode !== 200 && $httpCode !== 201) {
            $errorMsg = $chargeResponse['message'] ?? ($chargeResponse['error']['message'] ?? "HTTP $httpCode Error");
            error_log("❌ HTTP Error: $errorMsg");
            throw new Exception($errorMsg);
        }

        // Check if data exists
        if (!isset($chargeResponse['data'])) {
            error_log("❌ No 'data' field in response");
            error_log("Response keys: " . implode(', ', array_keys($chargeResponse)));
            throw new Exception("Invalid response structure from Flutterwave");
        }

        $chargeData = $chargeResponse['data'];
        error_log("✅ Charge Data Retrieved: " . json_encode($chargeData, JSON_PRETTY_PRINT));

        // Extract charge details
        $chargeId = $chargeData['id'] ?? null;
        $chargeStatus = $chargeData['status'] ?? null;
        
        error_log("Charge ID: $chargeId, Status: $chargeStatus");

        if (!$chargeId) {
            error_log("❌ No charge ID in response");
            throw new Exception("No charge ID returned from Flutterwave");
        }

        // Generate payment link
        $paymentLink = null;

        // Check for next_action with redirect
        if (isset($chargeData['next_action']) && is_array($chargeData['next_action'])) {
            error_log("Next Action: " . json_encode($chargeData['next_action']));
            
            if (isset($chargeData['next_action']['redirect_url']['url'])) {
                $paymentLink = $chargeData['next_action']['redirect_url']['url'];
                error_log("✅ Redirect URL: " . substr($paymentLink, 0, 100) . "...");
            } else if (isset($chargeData['next_action']['payment_instruction'])) {
                $paymentLink = "Payment instruction: " . ($chargeData['next_action']['payment_instruction']['note'] ?? 'Check email');
                error_log("✅ Payment Instruction: $paymentLink");
            }
        }

        // Fallback link if no redirect
        if (!$paymentLink) {
            $paymentLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . 
                          dirname($_SERVER['PHP_SELF']) . '/payment-success.php?reference=' . $reference . 
                          '&charge_id=' . $chargeId . '&status=' . $chargeStatus;
            error_log("✅ Using fallback link");
        }

        // Get currency for response
        $countryInfo = $method === 'mobile_money'
            ? ($networkCountryMap[$selectedNetwork] ?? $networkCountryMap['MTN'])
            : ['currency' => 'NGN', 'name' => 'Nigeria'];

        error_log("=== PAYMENT CREATION SUCCESS ===\n");

        $result = [
            'success' => true,
            'link' => $paymentLink,
            'reference' => $reference,
            'chargeId' => $chargeId,
            'status' => $chargeStatus,
            'paymentMethod' => $method,
            'currency' => $countryInfo['currency'],
            'country' => $countryInfo['name'],
            'message' => "Payment link created successfully"
        ];

        if ($method === 'mobile_money') {
            $result['selectedOption'] = $selectedNetwork;
        }

        return $result;
    }

    /**
     * Verify Payment Status
     */
    public function verifyPayment($reference) {
        error_log("📥 Verifying payment: $reference");

        try {
            $token = $this->getAccessToken();
        } catch (Exception $e) {
            error_log("❌ Failed to get token for verification: " . $e->getMessage());
            throw new Exception("Authentication failed");
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://developersandbox-api.flutterwave.com/charges/$reference/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("❌ Payment verification failed: HTTP $httpCode");
            throw new Exception("Payment verification failed");
        }

        $data = json_decode($response, true);
        error_log("✅ Payment verified: " . json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }
}

?>
