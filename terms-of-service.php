<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Void Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }
        
        nav { position: fixed; top: 0; width: 100%; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; z-index: 1000; background: rgba(0, 0, 0, 0.95); border-bottom: 1px solid #333; }
        .logo { font-size: 24px; font-weight: bold; color: #FFD93D; cursor: pointer; }
        .nav-links { display: flex; gap: 20px; list-style: none; }
        .nav-links a { color: #fff; text-decoration: none; transition: 0.3s; }
        .nav-links a:hover { color: #FFD93D; }
        
        .container { max-width: 900px; margin: 100px auto; padding: 40px 20px; }
        .header { text-align: center; margin-bottom: 50px; }
        .header h1 { font-size: 42px; color: #FFD93D; margin-bottom: 10px; }
        .header p { color: #ccc; font-size: 16px; }
        
        .content { background: #111; padding: 40px; border-radius: 10px; border: 1px solid #333; }
        .content h2 { font-size: 24px; color: #FFD93D; margin-top: 30px; margin-bottom: 15px; }
        .content h2:first-child { margin-top: 0; }
        .content h3 { font-size: 18px; color: #FFD93D; margin-top: 20px; margin-bottom: 10px; }
        .content p { color: #ccc; line-height: 1.8; margin-bottom: 15px; }
        .content ul, .content ol { color: #ccc; margin-left: 20px; margin-bottom: 15px; line-height: 1.8; }
        .content li { margin-bottom: 8px; }
        
        .back-btn { display: inline-block; margin-top: 30px; padding: 12px 25px; background: #FFD93D; color: #000; text-decoration: none; border-radius: 25px; font-weight: bold; transition: 0.3s; }
        .back-btn:hover { background: #FFE66D; transform: translateY(-2px); }
        
        footer { background: #111; padding: 40px 20px; text-align: center; color: #ccc; border-top: 1px solid #333; margin-top: 60px; }
        
        @media (max-width: 768px) {
            nav { padding: 15px 20px; }
            .container { margin-top: 120px; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo" onclick="window.location.href='index.php'">VOID FOOD</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" style="color: #FF6B6B;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.html">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>⚖️ Terms of Service</h1>
            <p>Last Updated: <?php echo date('M d, Y'); ?></p>
        </div>

        <div class="content">
            <h2>1. Agreement to Terms</h2>
            <p>By accessing and using the Void Food Collection website and services, you agree to be bound by these Terms of Service. If you do not agree to abide by the above, please do not use this service.</p>

            <h2>2. Use License</h2>
            <p>Permission is granted to temporarily download one copy of the materials (information or software) on Void Food Collection's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
            <ul>
                <li>Modifying or copying the materials</li>
                <li>Using the materials for any commercial purpose or for any public display</li>
                <li>Attempting to decompile or reverse engineer any software contained on the website</li>
                <li>Removing any copyright or other proprietary notations from the materials</li>
                <li>Transferring the materials to another person or "mirroring" the materials on any other server</li>
            </ul>

            <h2>3. Disclaimer</h2>
            <p>The materials on Void Food Collection's website are provided on an 'as is' basis. Void Food Collection makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

            <h2>4. Limitations</h2>
            <p>In no event shall Void Food Collection or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on Void Food Collection's website, even if Void Food Collection or an authorized representative has been notified orally or in writing of the possibility of such damage.</p>

            <h2>5. Accuracy of Materials</h2>
            <p>The materials appearing on Void Food Collection's website could include technical, typographical, or photographic errors. Void Food Collection does not warrant that any of the materials on the website are accurate, complete, or current. Void Food Collection may make changes to the materials contained on the website at any time without notice.</p>

            <h2>6. Materials Reference</h2>
            <p>Void Food Collection has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by Void Food Collection of the site. Use of any such linked website is at the user's own risk.</p>

            <h2>7. Modifications</h2>
            <p>Void Food Collection may revise these terms of service for the website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>

            <h2>8. Governing Law</h2>
            <p>These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which Void Food Collection operates, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>

            <h2>9. User Accounts</h2>
            <p>When you create an account, you are responsible for maintaining the confidentiality of your password and account information. You agree to accept responsibility for all activities that occur under your account. You must notify us immediately of any unauthorized use of your account.</p>

            <h2>10. Ordering and Payment</h2>
            <ul>
                <li>All orders are subject to acceptance by Void Food Collection</li>
                <li>Prices are subject to change without notice</li>
                <li>We reserve the right to cancel any order</li>
                <li>Payment must be received before order fulfillment</li>
                <li>We accept payment through Flutterwave and other authorized payment methods</li>
            </ul>

            <h2>11. Delivery</h2>
            <p>Delivery times are estimates and not guaranteed. Void Food Collection is not responsible for delays caused by events beyond our control. Customers are responsible for ensuring someone is available to receive the order at the delivery address.</p>

            <h2>12. Returns and Refunds</h2>
            <p>Items must be reported as unsatisfactory within 24 hours of delivery. Refunds are issued based on our discretion after investigation of the claim. Refunds will be processed to the original payment method within 5-7 business days.</p>

            <h2>13. Prohibited Conduct</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Violate any applicable laws or regulations</li>
                <li>Infringe on any intellectual property rights</li>
                <li>Harass, abuse, or threaten other users</li>
                <li>Attempt to gain unauthorized access to our systems</li>
                <li>Post false or misleading information</li>
                <li>Use automated tools to access our website</li>
            </ul>

            <h2>14. Limitation of Liability</h2>
            <p>In no event shall Void Food Collection, its directors, employees, or agents be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the website or services.</p>

            <h2>15. Contact Information</h2>
            <p>If you have any questions about these Terms of Service, please contact us at:</p>
            <ul>
                <li><strong>Email:</strong> support@voidfood.com</li>
                <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                <li><strong>Address:</strong> 123 Gourmet Street, Food City, FC 12345</li>
            </ul>

            <a href="javascript:history.back()" class="back-btn">← Go Back</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Void Food Collection. All rights reserved.</p>
    </footer>

</body>
</html>
