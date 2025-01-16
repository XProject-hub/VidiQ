<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Fetch all resellers
$resellers = $db->query("SELECT * FROM resellers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resellers - VidiQ</title>
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
                <li><a href="/admin/streams/index.php">Streams</a></li>
                <li><a href="/admin/reseller/index.php" class="active">Resellers</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Resellers</h1>
            <a href="/admin/reseller/add.php" class="button">Add New Reseller</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reseller = $resellers->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?= $reseller['id'] ?></td>
                            <td><?= htmlspecialchars($reseller['username']) ?></td>
                            <td><?= htmlspecialchars($reseller['email']) ?></td>
                            <td>
                                <a href="/admin/reseller/edit.php?id=<?= $reseller['id'] ?>" class="button">Edit</a>
                                <a href="/admin/reseller/delete.php?id=<?= $reseller['id'] ?>" class="button delete">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
