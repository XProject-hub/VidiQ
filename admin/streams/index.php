<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Fetch all streams
$streams = $db->query("SELECT * FROM streams");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streams - VidiQ</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="/public/images/logo.png" alt="VidiQ Logo">
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="/admin/dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/users/index.php">Users</a></li>
                <li><a href="/admin/streams/index.php" class="active">Streams</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Streams</h1>
            <a href="/admin/streams/add.php" class="button">Add New Stream</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Stream Name</th>
                        <th>URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stream = $streams->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?= $stream['id'] ?></td>
                            <td><?= htmlspecialchars($stream['name']) ?></td>
                            <td><?= htmlspecialchars($stream['url']) ?></td>
                            <td>
                                <a href="/admin/streams/edit.php?id=<?= $stream['id'] ?>" class="button">Edit</a>
                                <a href="/admin/streams/delete.php?id=<?= $stream['id'] ?>" class="button delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
