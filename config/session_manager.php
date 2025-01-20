<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$role = $_SESSION['role'] ?? 'Viewer'; // Default role: Viewer

// Function to check and enforce role
function checkRole($requiredRole) {
    global $role;

    if ($role !== $requiredRole && $role !== 'Admin') {
        header('HTTP/1.1 403 Forbidden');
        echo "Access denied!";
        exit;
    }
}

// Enforce the required role before proceeding with further code
checkRole('Admin'); // Example: Restrict access to Admins only

// Your other logic here...
?>
