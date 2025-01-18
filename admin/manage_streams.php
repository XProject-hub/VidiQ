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
    <title>Manage Streams</title>
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
    <h1>Manage Streams</h1>
    <button id="add-stream-btn" class="btn btn-primary">Add New Stream</button>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Stream Name</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="stream-table-body">
            <!-- Streams will be dynamically loaded here -->
        </tbody>
    </table>
</main>
<script src="/public/js/manage_streams.js"></script>
</body>
</html>
