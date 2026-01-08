<?php
require 'config.php';

$response = ["status" => false, "message" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  // SIGNUP
  if ($_POST['action'] === 'signup') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($email) || empty($password)) {
      $response = ["status" => false, "message" => "Email and password are required"];
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $response = ["status" => false, "message" => "Invalid email format"];
    } else if (strlen($password) < 6) {
      $response = ["status" => false, "message" => "Password must be at least 6 characters"];
    } else {
      $pass_hash = password_hash($password, PASSWORD_BCRYPT);

      $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
      if (!$stmt) {
        $response = ["status" => false, "message" => "Database error: " . $conn->error];
      } else {
        $stmt->bind_param("ss", $email, $pass_hash);

        if ($stmt->execute()) {
          $_SESSION['user_id'] = $conn->insert_id;
          $_SESSION['user_email'] = $email;
          $response = ["status" => true, "message" => "Signup successful"];
        } else if ($conn->errno == 1062) {
          $response = ["status" => false, "message" => "Email already exists"];
        } else {
          $response = ["status" => false, "message" => "Error creating account"];
        }
        $stmt->close();
      }
    }
  }

  // LOGIN
  else if ($_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
      $response = ["status" => false, "message" => "Email and password are required"];
    } else {
      $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email = ?");
      if (!$stmt) {
        $response = ["status" => false, "message" => "Database error"];
      } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
          $user = $result->fetch_assoc();
          if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $response = ["status" => true, "message" => "Login successful"];
          } else {
            $response = ["status" => false, "message" => "Invalid password"];
          }
        } else {
          $response = ["status" => false, "message" => "Email not found"];
        }
        $stmt->close();
      }
    }
  }

  // RESET REQUEST
  else if ($_POST['action'] === 'reset_request') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
      $response = ["status" => false, "message" => "Email is required"];
    } else {
      $token = bin2hex(random_bytes(32));
      $expiry = date("Y-m-d H:i:s", time() + 3600);

      $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
      if (!$stmt) {
        $response = ["status" => false, "message" => "Database error"];
      } else {
        $stmt->bind_param("sss", $token, $expiry, $email);
        if ($stmt->execute()) {
          $response = ["status" => true, "message" => "Reset link sent to your email", "token" => $token];
        } else {
          $response = ["status" => false, "message" => "Error processing request"];
        }
        $stmt->close();
      }
    }
  }

  // RESET PASSWORD
  else if ($_POST['action'] === 'reset_password') {
    $token = trim($_POST['token'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($token) || empty($password)) {
      $response = ["status" => false, "message" => "Token and password are required"];
    } else if (strlen($password) < 6) {
      $response = ["status" => false, "message" => "Password must be at least 6 characters"];
    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);

      $stmt = $conn->prepare("
        UPDATE users 
        SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL
        WHERE reset_token = ? AND reset_token_expiry > NOW()
      ");
      if (!$stmt) {
        $response = ["status" => false, "message" => "Database error"];
      } else {
        $stmt->bind_param("ss", $hash, $token);
        if ($stmt->execute()) {
          if ($stmt->affected_rows > 0) {
            $response = ["status" => true, "message" => "Password updated successfully"];
          } else {
            $response = ["status" => false, "message" => "Invalid or expired reset token"];
          }
        } else {
          $response = ["status" => false, "message" => "Error updating password"];
        }
        $stmt->close();
      }
    }
  }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
