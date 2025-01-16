<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');
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
                <li><a href="/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/users/index.php">Users</a></li>
                <li><a href="/admin/streams/index.php">Streams</a></li>
                <li><a href="/admin/reseller/index.php" class="active">Resellers</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Reseller Management</h1>
            <a href="add.php" class="button">Add New Reseller</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resellers->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= $row['status'] == 1 ? 'Active' : 'Inactive' ?></td>
                            <td>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="button-small">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="button-small danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
