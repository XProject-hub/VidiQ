<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VidiQ</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="/images/logo.png" alt="VidiQ Logo">
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="/admin/dashboard.php">Dashboard</a></li>
                <li class="dropdown">
                    <a href="#">Manage</a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/users/index.php">Users</a></li>
                        <li><a href="/admin/media/index.php">Media</a></li>
                        <li><a href="/admin/settings/index.php">Settings</a></li>
                    </ul>
                </li>
                <li><a href="/admin/logs/index.php">Logs</a></li>
                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Welcome to the Admin Dashboard</h1>
            <p>Manage your IPTV panel using the menu above.</p>
        </div>
    </main>
</body>
</html>
