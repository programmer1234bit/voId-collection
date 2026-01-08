<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Using empty string for XAMPP root user password
define('DB_NAME', 'void_food');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection and LOG the error (but don't die here)
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    // Set error flag instead of dying
    $db_error = $conn->connect_error;
} else {
    $db_error = null;
    // Set charset
    $conn->set_charset("utf8mb4");
}

// --- Aljumah Payment System Configuration ---
define('FLW_CLIENT_ID', '572f5f0d-05e8-499f-8497-50baaebd49e6');
define('FLW_CLIENT_SECRET', 'd2ViENaGmg54gnWNoNooQkAkKiQ1CB50');
define('FLW_SECRET_KEY', 'd2ViENaGmg54gnWNoNooQkAkKiQ1CB50');
define('FLW_ENCRYPTION_KEY', 'JVTowU8tfz2XE2BwwNDhsHJDV3O0atOe+Zrkk7oWBkM=');
define('FLW_WEBHOOK_SECRET_HASH', 'My_Very_Long_And_Secure_Flutterwave_Hash_For_Samson_2025_Success');

?>