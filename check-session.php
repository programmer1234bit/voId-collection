<?php
require 'config.php';

$response = ["logged_in" => false];

if (isset($_SESSION['user_id'])) {
    $response["logged_in"] = true;
    $response["user_id"] = $_SESSION['user_id'];
    $response["user_email"] = $_SESSION['user_email'] ?? null;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
