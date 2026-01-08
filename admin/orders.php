<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("
    SELECT o.*, u.email, o.phone
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");

$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders - Admin Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: #000; color: #fff; }

.admin-layout { display: flex; min-height: 100vh; }

.sidebar { width: 250px; background: #111; padding: 20px; position: fixed; height: 100vh; overflow-y: auto; border-right: 1px solid #333; }
.sidebar-logo { font-size: 24px; font-weight: bold; color: #FFD93D; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
.sidebar-menu { list-style: none; }
.sidebar-menu li { margin-bottom: 15px; }
.sidebar-menu a { color: #ccc; text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 10px 15px; border-radius: 6px; transition: 0.3s; }
.sidebar-menu a:hover { background: #1a1a1a; color: #FFD93D; }
.sidebar-menu a.active { background: #FFD93D; color: #000; font-weight: bold; }

.main-content { margin-left: 250px; padding: 20px; flex: 1; }

.navbar { background: #111; padding: 15px 30px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border: 1px solid #333; }
.navbar-title { font-size: 24px; color: #FFD93D; font-weight: bold; }
.logout-btn { background: #FF6B6B; color: #fff; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }

.content-area { background: #111; padding: 30px; border-radius: 8px; }

table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table th { background: #1a1a1a; padding: 12px; text-align: left; color: #FFD93D; font-weight: 600; border-bottom: 2px solid #333; }
table td { padding: 12px; border-bottom: 1px solid #333; }
table tr:hover { background: #1a1a1a; }

.status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; }
.status-pending { background: #FFD93D; color: #000; }
.status-completed { background: #6BCF7F; color: #000; }
.status-cancelled { background: #FF6B6B; color: #fff; }

.empty { text-align: center; padding: 60px 20px; color: #ccc; }
.empty i { font-size: 80px; color: #FFD93D; opacity: 0.3; margin-bottom: 20px; }

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    .main-content { margin-left: 200px; }
    table { font-size: 12px; }
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
            <li><a href="orders.php" class="active"><i class="fas fa-boxes"></i> Orders</a></li>
            <li><a href="menu.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="navbar">
            <div class="navbar-title">📦 Customer Orders</div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="content-area">
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Email</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></td>
                                <td>TZS <?php echo number_format($order['amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo strtoupper($order['status']); ?>
                                    </span>
                                </td>
                                <td><small style="color: #999;"><?php echo substr($order['tx_id'] ?? 'N/A', 0, 10) . '...'; ?></small></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">
                    <div><i class="fas fa-inbox"></i></div>
                    <p>No orders found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
