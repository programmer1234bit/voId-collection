<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_email = $_SESSION['admin_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $admin_id = $_SESSION['admin_id'];

    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($current, $user['password_hash'])) {
        if ($new === $confirm) {
            if (strlen($new) >= 8) {
                $new_hash = password_hash($new, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->bind_param("si", $new_hash, $admin_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Password changed successfully.";
                } else {
                    $_SESSION['error'] = "Failed to update password.";
                }
            } else {
                $_SESSION['error'] = "New password must be at least 8 characters.";
            }
        } else {
            $_SESSION['error'] = "New passwords do not match.";
        }
    } else {
        $_SESSION['error'] = "Current password is incorrect.";
    }
    header("Location: dashboard.php");
    exit;
}

// Get statistics
$orders_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$menu_count = $conn->query("SELECT COUNT(*) as count FROM menu")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(amount) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Void Food</title>
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

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #111;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #FFD93D;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #FFD93D;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #ccc;
            font-size: 14px;
        }

        .content-area {
            background: #111;
            padding: 30px;
            border-radius: 8px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary {
            background: #FFD93D;
            color: #000;
        }

        .btn-primary:hover {
            background: #FFE66D;
        }

        .btn-danger {
            background: #FF6B6B;
            color: #fff;
        }

        .btn-danger:hover {
            background: #ff5555;
        }

        .btn-edit {
            background: #667eea;
            color: #fff;
        }

        .btn-edit:hover {
            background: #5568d3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
        }

        table tr:hover {
            background: #1a1a1a;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .main-content {
                margin-left: 200px;
            }

            .stats {
                grid-template-columns: 1fr;
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
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-boxes"></i> Orders</a></li>
                <li><a href="payments.php"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
                <li><a href="menu.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="navbar">
                <div class="navbar-title">📊 Admin Dashboard</div>
                <div class="navbar-right">
                    <div class="user-info">
                        <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                        <span><?php echo htmlspecialchars($admin_email); ?></span>
                    </div>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $orders_count; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $users_count; ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $menu_count; ?></div>
                    <div class="stat-label">Menu Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">TZS <?php echo number_format($total_revenue); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>

            <!-- Welcome -->
            <div class="content-area">
                <?php if (isset($_SESSION['message'])): ?>
                    <div style="background: #4CAF50; color: white; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                        <?php echo $_SESSION['message'];
                        unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div style="background: #f44336; color: white; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <h2 style="color: #FFD93D; margin-bottom: 20px;">Welcome to Admin Panel 👋</h2>
                <p style="color: #ccc; line-height: 1.8;">
                    This is your central hub for managing the Void Food Collection platform. Use the sidebar to navigate
                    to different sections:
                </p>
                <ul style="color: #ccc; margin-top: 20px; margin-left: 20px; line-height: 1.8;">
                    <li><strong>Dashboard:</strong> View key statistics and system overview</li>
                    <li><strong>Orders:</strong> Manage and track customer orders</li>
                    <li><strong>Menu Items:</strong> Add, edit, and delete food items</li>
                    <li><strong>Users:</strong> View and manage customer accounts</li>
                </ul>
            </div>

            <!-- Profile Settings -->
            <div class="content-area" style="margin-top: 30px;">
                <h3 style="color: #FFD93D; margin-bottom: 20px;">Change Password</h3>
                <form method="post">
                    <div style="margin-bottom: 15px;">
                        <label for="current_password" style="display: block; margin-bottom: 5px; color: #ccc;">Current
                            Password:</label>
                        <input type="password" id="current_password" name="current_password" required
                            style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; border-radius: 5px; color: #fff;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="new_password" style="display: block; margin-bottom: 5px; color: #ccc;">New
                            Password:</label>
                        <input type="password" id="new_password" name="new_password" required
                            style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; border-radius: 5px; color: #fff;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label for="confirm_password" style="display: block; margin-bottom: 5px; color: #ccc;">Confirm
                            New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            style="width: 100%; padding: 10px; background: #1a1a1a; border: 1px solid #333; border-radius: 5px; color: #fff;">
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>