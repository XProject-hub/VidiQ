<?php
// /home/vidiq/public/index.php
// This page could check if the panel is installed, and if so, redirect to the login page.

// Check for existence of config file to decide if installation is complete.
if (file_exists(__DIR__ . '/../config/config.php')) {
    // Redirect to login (adjust the path as needed)
    header("Location: ../admin/login.php");
    exit;
} else {
    echo "Panel not installed. Please run the installer.";
}
?>
