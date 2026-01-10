<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_admin'])) {
    $user_id = intval($_POST['user_id']);
    $stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE id = ? AND is_admin = 0");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "User promoted to admin successfully.";
    } else {
        $_SESSION['error'] = "Failed to promote user.";
    }
    header("Location: users.php");
    exit;
}

$result = $conn->query("SELECT id, email, created_at FROM users WHERE is_admin = 0 ORDER BY created_at DESC");
$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Panel</title>
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

        .logout-btn {
            background: #FF6B6B;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
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
        }

        table tr:hover {
            background: #1a1a1a;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #ccc;
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
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-crown"></i> Admin
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="fas fa-boxes"></i> Orders</a></li>
                <li><a href="payments.php"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
                <li><a href="menu.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="navbar">
                <div class="navbar-title">👥 Customer Users</div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

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
                <?php if (count($users) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td><span style="color: #6BCF7F; font-weight: bold;">✓ Active</span></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="make_admin" class="btn btn-primary">Make Admin</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty">
                        <div style="font-size: 80px; margin-bottom: 20px;">👥</div>
                        <p>No customers yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>