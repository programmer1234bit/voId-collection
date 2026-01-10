<?php
require 'config.php';

// Destroy session
session_destroy();

// Clear session data
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Logging out...</title>
    <script>
        // Security Hygiene: Clear all local storage values (Carts, etc.) 
        // before leaving the application
        localStorage.clear();

        // Redirect to homepage
        window.location.href = "index.php";
    </script>
</head>

<body
    style="background: #000; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div style="text-align: center;">
        <h2 style="color: #FFD93D;">Logging out...</h2>
        <p>Clearing secure session data...</p>
    </div>
</body>

</html>
<?php
exit;
?>