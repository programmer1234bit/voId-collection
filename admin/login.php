<?php
require '../config.php';

if ($db_error) {
    die("❌ Database connection error: $db_error");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = '❌ Please enter email and password';
    } else {
        // Escape for safety
        $email = $conn->real_escape_string($email);
        
        // Check if user exists and is admin
        $result = $conn->query("SELECT id, email, password_hash, is_admin FROM users WHERE email = '$email' LIMIT 1");
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify admin status and password
            if ($user['is_admin'] == 1 && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
                header("Location: dashboard.php");
                exit;
            } else if ($user['is_admin'] == 0) {
                $error = '❌ This account is not an admin account';
            } else {
                $error = '❌ Invalid password';
            }
        } else {
            $error = '❌ Admin account not found';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Void Food</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #1a1a1a, #2d1b00); min-height: 100vh; display: flex; align-items: center; justify-content: center; }

.login-container { background: #111; padding: 50px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); max-width: 400px; width: 100%; border: 1px solid rgba(255,217,61,0.2); }

.login-header { text-align: center; margin-bottom: 40px; }
.login-header h1 { color: #FFD93D; font-size: 32px; margin-bottom: 10px; }
.login-header p { color: #ccc; }

.form-group { margin-bottom: 20px; }
.form-group label { display: block; color: #FFD93D; margin-bottom: 8px; font-weight: 500; }
.form-group input { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #333; border-radius: 6px; color: #fff; font-size: 14px; transition: 0.3s; }
.form-group input:focus { outline: none; border-color: #FFD93D; box-shadow: 0 0 10px rgba(255,217,61,0.3); }

.login-btn { width: 100%; background: linear-gradient(45deg, #FF6B6B, #FFD93D); color: #fff; padding: 12px; border: none; border-radius: 6px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; }
.login-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,107,107,0.3); }

.message { padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid; }
.error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.success { background: #d4edda; color: #155724; border-color: #c3e6cb; }

.credentials-info { background: #1a1a1a; padding: 15px; border-radius: 6px; margin-top: 20px; font-size: 13px; color: #ccc; border-left: 3px solid #FFD93D; }
.credentials-info strong { color: #FFD93D; }

.back-link { text-align: center; margin-top: 20px; }
.back-link a { color: #FFD93D; text-decoration: none; font-size: 14px; }
.back-link a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>🛠️ Admin Panel</h1>
        <p>Void Food Collection Management</p>
    </div>

    <?php if ($error): ?>
        <div class="message error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" placeholder="admin@voidfood.com" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="login-btn">Login to Admin Panel</button>
    </form>

    <div class="credentials-info">
        <strong>📧 Default Admin Credentials:</strong><br>
        Email: <strong>admin@voidfood.com</strong><br>
        Password: <strong>Admin@123456</strong><br>
        <em style="color: #FF6B6B;">⚠️ Change password after first login!</em>
    </div>

    <div class="back-link">
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</div>

</body>
</html>
