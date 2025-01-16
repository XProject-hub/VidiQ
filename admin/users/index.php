<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

// Connect to SQLite database
$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Fetch all users
$result = $db->query("SELECT * FROM users");
$users = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - VidiQ</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="/public/images/logo.png" alt="VidiQ Logo">
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
            <h1>Manage Users</h1>
            <a href="/admin/users/add.php" class="button">Add New User</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <a href="/admin/users/edit.php?id=<?= $user['id'] ?>" class="button">Edit</a>
                                <a href="/admin/users/delete.php?id=<?= $user['id'] ?>" class="button danger" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
