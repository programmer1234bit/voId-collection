<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_email = $_SESSION['admin_email'];

// Fetch all payment logs
$result = $conn->query("SELECT * FROM payment_logs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History - Admin</title>
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

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #111;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid #333;
        }

        .sidebar-logo {
            font-size: 24px;
            font-weight: bold;
            color: #FFD93D;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 15px;
        }

        .sidebar-menu a {
            color: #ccc;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .sidebar-menu a:hover {
            background: #1a1a1a;
            color: #FFD93D;
        }

        .sidebar-menu a.active {
            background: #FFD93D;
            color: #000;
            font-weight: bold;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .navbar {
            background: #111;
            padding: 15px 30px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border: 1px solid #333;
        }

        .navbar-title {
            font-size: 24px;
            color: #FFD93D;
            font-weight: bold;
        }

        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logout-btn {
            background: #FF6B6B;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #ff5555;
        }

        .content-area {
            background: #111;
            padding: 30px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #1a1a1a;
            padding: 12px;
            text-align: left;
            color: #FFD93D;
            font-weight: 600;
            border-bottom: 2px solid #333;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #333;
            color: #ccc;
        }

        table tr:hover {
            background: #1a1a1a;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-successful {
            background: #4CAF50;
            color: white;
        }

        .status-failed {
            background: #f44336;
            color: white;
        }

        .status-pending {
            background: #ff9800;
            color: black;
        }

        .ref-code {
            font-family: monospace;
            background: #222;
            padding: 2px 6px;
            border-radius: 4px;
            color: #FFD93D;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }
        }
    </style>
</head>

<body>

    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-crown"></i> Admin
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-boxes"></i> Orders</a></li>
                <li><a href="payments.php" class="active"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
                <li><a href="menu.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="navbar">
                <div class="navbar-title">💳 Payment History</div>
                <div class="navbar-right">
                    <div class="user-info">
                        <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                        <span>
                            <?php echo htmlspecialchars($admin_email); ?>
                        </span>
                    </div>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <div class="content-area">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>Customer Email</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo $row['id']; ?>
                                    </td>
                                    <td><span class="ref-code">
                                            <?php echo htmlspecialchars($row['reference']); ?>
                                        </span></td>
                                    <td>
                                        <?php echo htmlspecialchars($row['customer_email']); ?>
                                    </td>
                                    <td>
                                        <?php echo number_format($row['amount']); ?>
                                        <?php echo htmlspecialchars($row['currency']); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo ucfirst(str_replace('_', ' ', $row['payment_type'])); ?>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; color: #666;">
                        <i class="fas fa-money-check-alt" style="font-size: 48px; margin-bottom: 20px;"></i>
                        <p>No payment records found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>