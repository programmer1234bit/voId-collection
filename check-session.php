<?php
require 'config.php';

$response = ["logged_in" => false];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch username from user_profiles if it exists
    $stmt = $conn->prepare("SELECT u.email, up.username FROM users u LEFT JOIN user_profiles up ON u.id = up.user_id WHERE u.id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    
    $response["logged_in"] = true;
    $response["user_id"] = $user_id;
    $response["user_email"] = $user_data['email'] ?? ($_SESSION['user_email'] ?? null);
    
    // Provide display_name: username if set, otherwise email
    $response["display_name"] = !empty($user_data['username']) ? $user_data['username'] : $user_data['email'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
