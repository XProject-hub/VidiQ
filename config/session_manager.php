<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$role = $_SESSION['role'] ?? 'Viewer'; // Default role: Viewer

// Redirect based on role
function checkRole($requiredRole) {
    global $role;
    if ($role !== $requiredRole && $role !== 'Admin') {
        header('HTTP/1.1 403 Forbidden');
        echo "Access denied!";
        exit;
    }
}
?>
