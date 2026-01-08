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
    <title>Privacy Policy - Void Food</title>
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
            <li><a href="terms-of-service.php">Terms of Service</a></li>
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
            <h1>🔒 Privacy Policy</h1>
            <p>Last Updated: <?php echo date('M d, Y'); ?></p>
        </div>

        <div class="content">
            <h2>1. Introduction</h2>
            <p>Welcome to Void Food Collection ("we", "our", or "us"). We are committed to protecting your privacy and ensuring you have a positive experience on our website and services. This Privacy Policy explains how we collect, use, disclose, and otherwise process your personal information.</p>

            <h2>2. Information We Collect</h2>
            
            <h3>2.1 Information You Provide Directly</h3>
            <ul>
                <li><strong>Account Registration:</strong> Email address, password, phone number, and username</li>
                <li><strong>Order Information:</strong> Full name, delivery address, city, state, phone number</li>
                <li><strong>Payment Information:</strong> Transaction IDs and payment method preferences</li>
                <li><strong>Communication:</strong> Messages sent through our contact forms</li>
            </ul>

            <h3>2.2 Information Collected Automatically</h3>
            <ul>
                <li>Browser type and IP address</li>
                <li>Pages visited and time spent on our website</li>
                <li>Referring website or search terms used</li>
                <li>Device information and operating system</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <p>We use the information we collect for various purposes, including:</p>
            <ul>
                <li>Processing and delivering your orders</li>
                <li>Managing your account and providing customer support</li>
                <li>Sending order confirmations and updates</li>
                <li>Improving our website and services</li>
                <li>Responding to your inquiries and feedback</li>
                <li>Preventing fraud and ensuring security</li>
                <li>Complying with legal obligations</li>
            </ul>

            <h2>4. Sharing Your Information</h2>
            <p>We do not sell, trade, or share your personal information with third parties except:</p>
            <ul>
                <li><strong>Service Providers:</strong> Payment processors and delivery partners who need information to fulfill orders</li>
                <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
                <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale of assets</li>
            </ul>

            <h2>5. Data Security</h2>
            <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is completely secure.</p>

            <h2>6. Your Rights and Choices</h2>
            <ul>
                <li><strong>Access:</strong> You can access your account information at any time by logging into your profile</li>
                <li><strong>Correction:</strong> You may update or correct your information through your account settings</li>
                <li><strong>Deletion:</strong> You can request deletion of your account and associated data by contacting us</li>
                <li><strong>Opt-out:</strong> You can manage your communication preferences in your account settings</li>
            </ul>

            <h2>7. Cookies and Tracking</h2>
            <p>Our website may use cookies and similar tracking technologies to enhance your experience. These help us remember your preferences and analyze how you use our services. You can control cookie settings through your browser.</p>

            <h2>8. Third-Party Links</h2>
            <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review their privacy policies before providing any personal information.</p>

            <h2>9. Children's Privacy</h2>
            <p>Our services are not directed to children under the age of 13. We do not knowingly collect personal information from children under 13. If we become aware that we have collected such information, we will take steps to delete it.</p>

            <h2>10. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the new policy on our website and updating the "Last Updated" date. Your continued use of our services indicates your acceptance of the updated policy.</p>

            <h2>11. Contact Us</h2>
            <p>If you have questions about this Privacy Policy or our privacy practices, please contact us at:</p>
            <ul>
                <li><strong>Email:</strong> privacy@voidfood.com</li>
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
