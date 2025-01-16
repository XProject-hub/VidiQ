<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'vidiq_admin');
define('DB_PASSWORD', 'secure_password'); // Replace 'secure_password' with a real password generated during installation
define('DB_NAME', 'vidiq_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
