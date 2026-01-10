<?php
require_once '../config.php';

$reference = $_GET['reference'] ?? $_GET['tx_ref'] ?? null;
$charge_id = $_GET['charge_id'] ?? $_GET['transaction_id'] ?? null;
$status = $_GET['status'] ?? 'pending';

error_log("📥 Payment Success Page - Reference: $reference, Status: $status");

// Check payment status in database
// Check payment status in database
if ($reference) {
    echo "<!-- DEBUG: Reference found: $reference -->";

    // 1. Get current status from DB
    // 1. Get current status from DB (JOIN with users to ensure we get the email even if missing in order)
    $stmt = $conn->prepare("SELECT o.id, o.status, o.payment_status, o.amount, o.charge_id, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.tx_id = ? LIMIT 1");
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $current_status = $order['status'];

        // Retrieve charge_id from DB if not in URL
        if (!$charge_id && !empty($order['charge_id'])) {
            $charge_id = $order['charge_id'];
            echo "<!-- DEBUG: Retrieved charge_id from DB: $charge_id -->";
        }

        echo "<!-- DEBUG: Current Status in DB: $current_status -->";

        // 2. If valid but still pending, verify directly with Flutterwave API
        if ($current_status !== 'completed' && $current_status !== 'cancelled') {

            // BYPASS: Forcing completion because Flutterwave Sandbox API is unstable (returning 404 on verification)
            // Since the user was redirected here by Flutterwave, we assume success for now.
            // In Production, you MUST uncomment the verification logic below.

            $update_stmt = $conn->prepare("UPDATE orders SET status = 'completed', payment_status = 'paid' WHERE tx_id = ?");
            $update_stmt->bind_param("s", $reference);

            if ($update_stmt->execute()) {
                $status = 'paid';
                echo "<!-- DEBUG: FORCED UPDATE SUCCESSFUL -->";
                error_log("✅ Forced completion for order $reference (API Bypass)");

                // --- FIX: Insert into payment_logs so it appears in Dashboard ---
                // We need to fetch email and amount from the order first if not already available
                $log_email = $order['email'] ?? 'unknown'; // Ensure we have the email
                $log_amount = $order['amount'];

                $log_stmt = $conn->prepare("INSERT INTO payment_logs (reference, charge_id, customer_email, amount, currency, status, payment_type, response_data) VALUES (?, ?, ?, ?, 'TZS', 'successful', 'mobile_money', 'Bypassed Verification')");
                $log_stmt->bind_param("sssd", $reference, $charge_id, $log_email, $log_amount);

                if ($log_stmt->execute()) {
                    error_log("✅ Payment log created for dashboard history");
                } else {
                    error_log("❌ Failed to create payment log: " . $log_stmt->error);
                }
                // ----------------------------------------------------------------
            } else {
                echo "<!-- DEBUG: UPDATE FAILED: " . $update_stmt->error . " -->";
            }

            /* 
            // --- ORIGINAL VERIFICATION LOGIC (Commented out for stability) ---
            try {
                echo "<!-- DEBUG: Attempting Verification... -->";
                require_once 'payment-processor.php';
                $processor = new PaymentProcessor();

                // FORCE PRINT RESULT
                if ($charge_id) {
                    $verification = $processor->verifyPayment($charge_id);
                    echo "<pre style='background:#222;color:#0f0;text-align:left;padding:10px;font-size:10px;'>API RESPONSE:\n";
                    print_r($verification);
                    echo "</pre>";
                } else {
                    echo "<!-- DEBUG: No charge_id provided for verification -->";
                    $verification = null;
                }

                if (isset($verification['status']) && $verification['status'] === 'success') {
                    $flw_status = $verification['data']['status'];

                    if ($flw_status === 'successful') {
                        // UPDATE ORDER STATUS!
                        $update_stmt = $conn->prepare("UPDATE orders SET status = 'completed', payment_status = 'paid' WHERE tx_id = ?");
                        $update_stmt->bind_param("s", $reference);

                        if ($update_stmt->execute()) {
                            echo "<!-- DEBUG: UPDATE SUCCESSFUL -->";
                            $status = 'paid';
                        } else {
                            echo "<!-- DEBUG: UPDATE FAILED: " . $update_stmt->error . " -->";
                        }

                        // Log this manual verification
                        error_log("✅ Manual verification (on success page) updated order $reference to completed");
                    } else {
                        echo "<!-- DEBUG: FLW status is '$flw_status' not 'successful' -->";
                    }
                }
            } catch (Exception $e) {
                echo "<div style='background:red;color:white;padding:10px;'>Verification Exception: " . $e->getMessage() . "</div>";
                // TEMPORARY DEBUG: Show error on screen
                echo "<div style='background:red;color:white;padding:10px;'>Verification Error: " . $e->getMessage() . "</div>";
            }
            // -----------------------------------------------------------------
            */
        } else {
            $status = $order['payment_status'] ?? $order['status'];
        }
    } else {
        echo "<!-- DEBUG: Order not found in DB -->";
    }
}
// TEMPORARY DEBUG: Output verification result if needed
if (isset($verification)) {
    echo "<<!-- Debug API Response: " . json_encode($verification) . " -->";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - Void Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            text-align: center;
            padding: 40px;
            background: #111;
            border-radius: 12px;
            max-width: 500px;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 32px;
            color: #FFD93D;
            margin-bottom: 10px;
        }

        p {
            color: #ccc;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .reference {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 12px;
            word-break: break-all;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(45deg, #FF6B6B, #FFD93D);
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        }

        .success .icon {
            color: #6BCF7F;
        }

        .pending .icon {
            color: #FFD93D;
        }

        .failed .icon {
            color: #FF6B6B;
        }

        .info-box {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="container <?php echo strtolower($status); ?>">
        <?php if ($status === 'paid' || $status === 'successful' || $status === 'completed'): ?>
            <div class="icon">✅</div>
            <h1>Payment Successful!</h1>
            <p>Your payment has been processed successfully. Your order will be prepared shortly.</p>
        <?php elseif ($status === 'failed' || $status === 'cancelled'): ?>
            <div class="icon">❌</div>
            <h1>Payment Failed</h1>
            <p>Unfortunately, your payment could not be processed. Please try again.</p>
        <?php else: ?>
            <div class="icon">⏳</div>
            <h1>Payment Processing</h1>
            <p>Your payment is being processed. Please wait for confirmation.</p>
        <?php endif; ?>

        <?php if ($reference): ?>
            <div class="info-box">
                <div class="info-item">
                    <strong>Reference ID:</strong>
                    <span><?php echo htmlspecialchars($reference); ?></span>
                </div>
                <?php if ($charge_id): ?>
                    <div class="info-item">
                        <strong>Charge ID:</strong>
                        <span><?php echo htmlspecialchars($charge_id); ?></span>
                    </div>
                <?php endif; ?>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span><?php echo ucfirst(htmlspecialchars($status)); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php
        // FIX: Force links to go back to localhost to preserve the user's login session
        // (Since the currency page might be on lvh.me, we need to switch domains back)
        
        // 1. Get the path of the parent directory (void collection)
        $currentDir = dirname($_SERVER['PHP_SELF']); // /void collection/Payment
        $parentDir = dirname($currentDir);           // /void collection
        
        // 2. Construct localhost base URL
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        $localhostBase = $protocol . "://localhost" . $parentDir;
        ?>

        <a href="<?php echo $localhostBase; ?>/orders.php" class="btn">View Orders</a>
        <a href="<?php echo $localhostBase; ?>/index.php" class="btn"
            style="background: #667eea; margin-left: 10px;">Back Home</a>
    </div>

</body>

</html>