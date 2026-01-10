<?php
// Clear any previous output
ob_clean();

// Set proper headers FIRST
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Now include files
require_once '../config.php';
require 'payment-processor.php';

// Log incoming request
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

error_log("\n=== PROCESS PAYMENT REQUEST ===");
error_log("Input: " . json_encode($input, JSON_PRETTY_PRINT));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$input) {
            $input = $_POST;
        }

        $email = $input['email'] ?? null;
        $name = $input['name'] ?? null;
        $items = $input['items'] ?? []; // Get items for server-side calculation
        $paymentMethod = $input['paymentMethod'] ?? 'mobile_money';
        $network = $input['network'] ?? 'MTN';
        $cardType = $input['cardType'] ?? 'visa';

        // --- SECURITY FIX: RECALCULATE AMOUNT FROM DATABASE ---
        $calculatedAmount = 0;

        if (empty($items) || !is_array($items)) {
            throw new Exception("Cart is empty or invalid. Cannot process.");
        }

        // Prepare statement to fetch prices
        $stmt = $conn->prepare("SELECT price, name FROM menu WHERE id = ?");

        foreach ($items as $item) {
            $itemId = $item['id'] ?? 0;
            $qty = intval($item['qty'] ?? 1);

            if ($qty < 1)
                $qty = 1;

            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $dbPrice = floatval($row['price']);
                $lineTotal = $dbPrice * $qty;
                $calculatedAmount += $lineTotal;
                // Optional: Check if price changed and warn? 
            }
        }
        $stmt->close();

        // Add Delivery Fee (Fixed)
        $deliveryFee = 500;
        $calculatedAmount += $deliveryFee;

        // Use the SECURE calculated amount, ignore client input
        $amount = $calculatedAmount;
        // -----------------------------------------------------

        // Convert amount for card payments (TZS to NGN)
        if ($paymentMethod === 'card') {
            $amount = (int) round($amount * (1600 / 2300));
        }

        error_log("Parameters: SECURE_amount=$amount, email=$email, name=$name, method=$paymentMethod");

        // Validate inputs
        if (!$amount || !$email || !$name) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Missing required fields',
                'message' => 'Please provide amount, email, and name',
                'received' => $input
            ]);
            exit;
        }

        error_log("✅ Basic validation passed");

        $processor = new PaymentProcessor();
        $result = $processor->createPaymentLink($amount, $email, $name, $paymentMethod, $network, $cardType);

        error_log("✅ Payment processor returned successfully");
        error_log("Result: " . json_encode($result));

        // Ensure clean output
        http_response_code(200);
        echo json_encode($result);
        exit;

    } catch (Exception $e) {
        error_log("❌ Exception: " . $e->getMessage());
        error_log("File: " . $e->getFile() . " Line: " . $e->getLine());

        http_response_code(400);
        echo json_encode([
            'error' => "Failed to create payment link",
            'message' => $e->getMessage(),
            'details' => null
        ]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
?>