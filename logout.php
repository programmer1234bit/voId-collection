<?php
require 'config.php';

// Destroy session
session_destroy();

// Clear session data
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);

// Redirect to home
header("Location: index.php");
exit;
?>
