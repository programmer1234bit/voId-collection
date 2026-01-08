<?php
require_once '../config.php';

// Log all webhook requests
$request_body = file_get_contents('php://input');
$webhook_data = json_decode($request_body, true);

error_log("📡 Webhook received at: " . date('Y-m-d H:i:s'));
error_log("Webhook payload: " . json_encode($webhook_data, JSON_PRETTY_PRINT));

// Get signature from headers
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';
$expected_hash = FLW_WEBHOOK_SECRET_HASH;

error_log("Received signature: " . $signature);
error_log("Expected signature: " . $expected_hash);

// 1. Verify webhook signature
if (!$signature || ($signature !== $expected_hash)) {
    error_log("❌ Invalid webhook signature - request rejected");
    http_response_code(403);
    exit(json_encode(['error' => 'Invalid signature']));
}

error_log("✅ Webhook signature verified");

// 2. Send 200 OK response immediately
http_response_code(200);
echo json_encode(['status' => 'received']);

// 3. Process the webhook data
if ($webhook_data && isset($webhook_data['data'])) {
    $data = $webhook_data['data'];
    $event = $webhook_data['event'] ?? null;
    
    error_log("Event type: " . $event);

    // Handle charge.completed event
    if ($event === 'charge.completed') {
        $charge_id = $data['id'] ?? null;
        $status = $data['status'] ?? null;
        $tx_ref = $data['tx_ref'] ?? null;
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'TZS';
        $customer_email = $data['customer']['email'] ?? null;
        $payment_type = $data['payment_method']['type'] ?? 'unknown';

        error_log("💳 Processing payment:");
        error_log("  Charge ID: $charge_id | TX Reference: $tx_ref | Status: $status");

        if ($status === 'successful') {
            error_log("✅ Payment successful signal received!");

            // --- FIXED LOGIC: CHECK IF ALREADY COMPLETED INSTEAD OF JUST EXISTING ---
            $check_existing = $conn->query("SELECT id, status FROM orders WHERE tx_id = '$tx_ref' LIMIT 1");
            
            if ($check_existing && $check_existing->num_rows > 0) {
                $order_row = $check_existing->fetch_assoc();
                
                if ($order_row['status'] === 'completed') {
                    error_log("⚠️ Payment already marked as completed (duplicate webhook)");
                    exit;
                }

                // Update order status from pending to completed
                $update_result = $conn->query("UPDATE orders SET status = 'completed', payment_status = 'paid' WHERE tx_id = '$tx_ref'");
                
                if ($update_result) {
                    error_log("✅ Order status updated to 'completed'");
                    
                    // Log successful payment in payment_logs
                    $safe_body = $conn->real_escape_string($request_body);
                    $log_query = "INSERT INTO payment_logs (reference, charge_id, amount, currency, status, payment_type, customer_email, response_data) 
                                 VALUES ('$tx_ref', '$charge_id', $amount, '$currency', 'successful', '$payment_type', '$customer_email', '$safe_body')";
                    
                    if ($conn->query($log_query)) {
                        error_log("✅ Payment logged successfully");
                    } else {
                        error_log("❌ Error logging payment: " . $conn->error);
                    }
                } else {
                    error_log("❌ Error updating order: " . $conn->error);
                }
            } else {
                error_log("❌ Reference $tx_ref not found in orders table.");
            }

        } else if ($status === 'failed') {
            error_log("❌ Payment failed!");
            $conn->query("UPDATE orders SET status = 'cancelled', payment_status = 'failed' WHERE tx_id = '$tx_ref'");
        }
    }
    // Handle other events
    else if ($event === 'charge.updated') {
        $tx_ref = $data['tx_ref'] ?? null;
        $status = $data['status'] ?? null;
        if ($tx_ref && $status) {
            $conn->query("UPDATE orders SET status = '$status' WHERE tx_id = '$tx_ref'");
            error_log("✅ Order status updated to: $status");
        }
    }
} else {
    error_log("⚠️ Invalid webhook payload structure");
}
error_log("✅ Webhook processing completed\n");
?>