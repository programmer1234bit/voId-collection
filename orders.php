<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Void Food</title>
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
        }

        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.95);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #FFD93D;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #FFD93D;
        }

        .logout-btn {
            background: rgba(255, 107, 107, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255, 107, 107, 0.4);
            color: #FF6B6B !important;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #FF6B6B !important;
            color: #fff !important;
        }

        .container {
            max-width: 1000px;
            margin: 120px auto;
            padding: 20px;
        }

        .header {
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 42px;
            color: #FFD93D;
        }

        .order-card {
            background: #111;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #FFD93D;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-id {
            font-size: 18px;
            font-weight: bold;
            color: #FFD93D;
        }

        .order-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #FFD93D;
            color: #000;
        }

        .status-processing {
            background: #6BCF7F;
            color: #000;
        }

        .status-completed {
            background: #4CAF50;
            color: #fff;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .detail-item {
            color: #ccc;
        }

        .detail-item strong {
            color: #FFD93D;
        }

        .order-items {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: #ccc;
            font-size: 14px;
        }

        .empty-orders {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-orders i {
            font-size: 80px;
            color: #FFD93D;
            margin-bottom: 20px;
        }

        .empty-orders p {
            color: #ccc;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <nav>
        <div class="logo">VOID FOOD</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="header">
            <h1>My Orders 📦</h1>
        </div>

        <?php if (isset($_GET['order_confirmed']) && $_GET['order_confirmed'] === 'true'): ?>
            <div
                style="background: #4CAF50; color: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: center; font-weight: bold; font-size: 18px;">
                ✅ Order placed successfully! Your cart has been cleared.
            </div>
        <?php endif; ?>

        <div id="ordersList"></div>
    </div>

    <script>
        async function loadOrders() {
            const r = await fetch('api.php?action=get_orders');
            const d = await r.json();

            if (!d.orders || d.orders.length === 0) {
                document.getElementById('ordersList').innerHTML = `
            <div class="empty-orders">
                <i class="fas fa-inbox"></i>
                <p>No orders yet</p>
                <a href="menu.php" style="color: #FFD93D; text-decoration: none; font-weight: bold;">Start Ordering</a>
            </div>
        `;
                return;
            }

            let html = '';
            d.orders.forEach(order => {
                const statusClass = 'status-' + order.status;
                const items = JSON.parse(order.items || '[]');
                let itemsHtml = '';

                items.forEach(item => {
                    itemsHtml += `<div class="item"><span>${item.qty}x ${item.name}</span><span>TZS ${(item.price * item.qty).toLocaleString()}</span></div>`;
                });

                html += `<div class="order-card">
            <div class="order-header">
                <div class="order-id">Order #${order.id}</div>
                <span class="order-status ${statusClass}">${order.status.toUpperCase()}</span>
            </div>
            <div class="order-details">
                <div class="detail-item"><strong>Amount:</strong> ₦${parseInt(order.amount).toLocaleString()}</div>
                <div class="detail-item"><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</div>
                <div class="detail-item"><strong>Delivery:</strong> ${order.address}</div>
                <div class="detail-item"><strong>Phone:</strong> ${order.phone}</div>
            </div>
            <div class="order-items">${itemsHtml}</div>
        </div>`;
            });

            document.getElementById('ordersList').innerHTML = html;
        }

        window.onload = loadOrders;
    </script>

</body>

</html>