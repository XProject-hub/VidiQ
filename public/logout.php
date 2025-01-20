<?php
session_start();
if (session_status() == PHP_SESSION_ACTIVE) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
}

// Redirect to index.php
header('Location: /index.php');
exit;
?>
