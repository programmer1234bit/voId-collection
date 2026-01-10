<?php
require 'config.php'; // config.php handles session_start() and $conn

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- LOGIN ACTION ---
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['status' => false, 'message' => 'Email and password required']);
            exit;
        }

        $stmt = $conn->prepare("SELECT id, email, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            if ($user['is_admin']) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
            }

            echo json_encode([
                'status' => true,
                'message' => 'Login successful',
                'is_admin' => (bool) $user['is_admin']
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Invalid credentials']);
        }
        exit;
    }

    // --- REGISTER ACTION (Updated for Security) ---
    if ($action === 'register') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['status' => false, 'message' => 'Email and password required']);
            exit;
        }

        // Use prepared statement to check for existing email
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $check = $stmt_check->get_result();

        if ($check && $check->num_rows > 0) {
            echo json_encode(['status' => false, 'message' => 'Email already registered']);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hash);

        if ($stmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Account created successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Registration failed']);
        }
        exit;
    }

    // --- UPDATE PROFILE ACTION ---
    if ($action === 'update_profile') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $username = $_POST['username'] ?? '';

        $stmt = $conn->prepare("INSERT INTO user_profiles (user_id, username) VALUES (?, ?) ON DUPLICATE KEY UPDATE username = ?");
        $stmt->bind_param("iss", $user_id, $username, $username);

        if ($stmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Profile updated']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Update failed']);
        }
        exit;
    }

    // --- CREATE ORDER ACTION (Improved Debugging) ---
    if ($action === 'create_order') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $amount = floatval($_POST['amount'] ?? 0);
        $full_name = $_POST['fullName'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $tx_id = $_POST['tx_id'] ?? '';
        $charge_id = $_POST['charge_id'] ?? ''; // Added charge_id
        $items = $_POST['items'] ?? '[]';

        $email = $_POST['email'] ?? $_SESSION['user_email'] ?? ''; // Ensure email is captured

        $stmt = $conn->prepare("INSERT INTO orders (user_id, amount, full_name, email, phone, address, city, state, tx_id, charge_id, items, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("idsssssssss", $user_id, $amount, $full_name, $email, $phone, $address, $city, $state, $tx_id, $charge_id, $items);

        if ($stmt->execute()) {
            echo json_encode(['status' => true, 'message' => 'Order created', 'order_id' => $conn->insert_id]);
        } else {
            // Log database error for troubleshooting
            error_log("Order failed for user $user_id: " . $stmt->error);
            echo json_encode(['status' => false, 'message' => 'Order creation failed: ' . $stmt->error]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    // --- GET MENU ACTION ---
    if ($action === 'get_menu') {
        $result = $conn->query("SELECT * FROM menu ORDER BY category, name");
        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        echo json_encode(['items' => $items]);
        exit;
    }

    // --- GET ORDERS ACTION (Updated for Security) ---
    if ($action === 'get_orders') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Not logged in']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        // Use prepared statement instead of injecting $user_id directly
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        echo json_encode(['orders' => $orders]);
        exit;
    }

    // --- GET PAYMENTS ACTION (Updated for Security) ---
    if ($action === 'get_payments') {
        if (!isset($_SESSION['user_email'])) {
            echo json_encode(['status' => false, 'message' => 'Not logged in']);
            exit;
        }

        $email = $_SESSION['user_email'];
        // Use prepared statement instead of injecting $email directly
        $stmt = $conn->prepare("SELECT * FROM payment_logs WHERE customer_email = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        echo json_encode(['payments' => $payments]);
        exit;
    }
}

echo json_encode(['status' => false, 'message' => 'Invalid request']);
?>