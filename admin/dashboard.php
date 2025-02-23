<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VidiQ</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/manage_admins.php">Manage Admins</a></li>
                <li><a href="/admin/manage_resellers.php">Manage Resellers</a></li>
                <!-- Other admin links -->
            </ul>
        </nav>
    </header>
    <main>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <!-- Additional dashboard content here -->
    </main>
</body>
</html>
