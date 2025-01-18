<?php
require_once '../config/session_manager.php';

// Restrict access to Admin and Editor roles
checkRole('Editor');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management</title>
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
            <?php if ($userRole === 'Admin'): ?>
                <li><a href="/admin/server_management.php">Servers</a></li>
            <?php endif; ?>
            <li><a href="/admin/user_management.php">Users</a></li>
            <li><a href="/admin/content_management.php">Content</a></li>
            <li><a href="/logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<main>
    <h1>Content Management</h1>
    <section class="content-options">
        <div class="card">
            <h2>Streams</h2>
            <a href="/admin/manage_streams.php" class="btn btn-cyan">Manage Streams</a>
        </div>
        <div class="card">
            <h2>Movies</h2>
            <a href="/admin/manage_movies.php" class="btn btn-cyan">Manage Movies</a>
        </div>
        <div class="card">
            <h2>Series</h2>
            <a href="/admin/manage_series.php" class="btn btn-cyan">Manage Series</a>
        </div>
    </section>
</main>
</body>
</html>
