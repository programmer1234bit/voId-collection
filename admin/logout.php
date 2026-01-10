session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Logging out Admin...</title>
    <script>
        // Security Hygiene: Clear all local storage values
        localStorage.clear();

        // Redirect to admin login page
        window.location.href = "login.php";
    </script>
</head>

<body
    style="background: #000; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div style="text-align: center;">
        <h2 style="color: #FFD93D;">Admin Logout</h2>
        <p>Clearing secure dashboard data...</p>
    </div>
</body>

</html>
<?php
exit;
?>