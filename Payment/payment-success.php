<?php
require_once '../config.php';

$reference = $_GET['reference'] ?? null;
$charge_id = $_GET['charge_id'] ?? null;
$status = $_GET['status'] ?? 'pending';

error_log("📥 Payment Success Page - Reference: $reference, Status: $status");

// Check payment status in database
if ($reference) {
    $result = $conn->query("SELECT status, payment_status FROM orders WHERE tx_id = '$reference' LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $status = $order['payment_status'] ?? $order['status'];
        error_log("✅ Order found in database - Status: $status");
    }
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
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.container { text-align: center; padding: 40px; background: #111; border-radius: 12px; max-width: 500px; }
.icon { font-size: 80px; margin-bottom: 20px; }
h1 { font-size: 32px; color: #FFD93D; margin-bottom: 10px; }
p { color: #ccc; margin-bottom: 20px; font-size: 16px; }
.reference { background: #1a1a1a; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 12px; word-break: break-all; }
.btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; text-decoration: none; border-radius: 25px; font-weight: bold; transition: 0.3s; }
.btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,107,107,0.3); }
.success .icon { color: #6BCF7F; }
.pending .icon { color: #FFD93D; }
.failed .icon { color: #FF6B6B; }
.info-box { background: #1a1a1a; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left; }
.info-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
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

    <a href="orders.php" class="btn">View Orders</a>
    <a href="index.php" class="btn" style="background: #667eea; margin-left: 10px;">Back Home</a>
</div>

</body>
</html>
