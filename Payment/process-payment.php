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

        $amount = $input['amount'] ?? null;
        $email = $input['email'] ?? null;
        $name = $input['name'] ?? null;
        $paymentMethod = $input['paymentMethod'] ?? 'mobile_money';
        $network = $input['network'] ?? 'MTN';
        $cardType = $input['cardType'] ?? 'visa';

        // Convert amount for card payments (TZS to NGN)
        if ($paymentMethod === 'card') {
            $amount = (int)round($amount * (1600 / 2300));
        }

        error_log("Parameters: amount=$amount, email=$email, name=$name, method=$paymentMethod");

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
