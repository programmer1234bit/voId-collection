<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$action = $_GET['action'] ?? null;
$message = '';

// Delete category
if ($action === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Check if category is used in menu
    $check = $conn->query("SELECT COUNT(*) as count FROM menu WHERE category = (SELECT name FROM categories WHERE id = $id)");
    $row = $check->fetch_assoc();
    if ($row['count'] > 0) {
        $message = '❌ Cannot delete category: It is used by menu items';
    } else {
        if ($conn->query("DELETE FROM categories WHERE id = $id")) {
            $message = '✅ Category deleted successfully';
        } else {
            $message = '❌ Error deleting category';
        }
    }
}

// Add/Edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id = intval($_POST['id'] ?? 0);

    if ($name) {
        if ($id > 0) {
            // Edit
            if ($conn->query("UPDATE categories SET name='$name', description='$description' WHERE id=$id")) {
                $message = '✅ Category updated successfully';
            } else {
                $message = '❌ Error updating category';
            }
        } else {
            // Add
            if ($conn->query("INSERT INTO categories (name, description) VALUES ('$name', '$description')")) {
                $message = '✅ Category added successfully';
            } else {
                $message = '❌ Error adding category';
            }
        }
    } else {
        $message = '❌ Please enter category name';
    }
}

// Get all categories
$result = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get edit category data
$edit_category = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM categories WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $edit_category = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
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

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-section {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #FFD93D;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            background: #111;
            border: 1px solid #333;
            border-radius: 6px;
            color: #fff;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #FFD93D;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
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

        .btn-secondary {
            background: #667eea;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #5568d3;
        }

        .btn-danger {
            background: #FF6B6B;
            color: #fff;
        }

        .btn-danger:hover {
            background: #ff5555;
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

        .action-btns {
            display: flex;
            gap: 8px;
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
                <li><a href="categories.php" class="active"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="navbar">
                <div class="navbar-title">🏷️ Categories</div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="content-area">
                <div class="form-section">
                    <h3 style="color: #FFD93D; margin-bottom: 15px;">
                        <?php echo $edit_category ? '✏️ Edit Category' : '➕ Add New Category'; ?>
                    </h3>
                    <form method="POST">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Category Name *</label>
                            <input type="text" name="name"
                                value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description"
                                rows="3"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                            </button>
                            <?php if ($edit_category): ?>
                                <a href="categories.php" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <h3 style="color: #FFD93D; margin-bottom: 15px;">All Categories (<?php echo count($categories); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                <td><?php echo $category['created_at']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="?action=edit&id=<?php echo $category['id']; ?>"
                                            class="btn btn-secondary">Edit</a>
                                        <a href="?action=delete&id=<?php echo $category['id']; ?>" class="btn btn-danger"
                                            onclick="return confirm('Delete this category?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>