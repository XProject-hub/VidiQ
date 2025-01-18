<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $source = $_POST['source'];
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $db->prepare("INSERT INTO streams (name, source, status) VALUES (:name, :source, :status)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':source', $source, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_INTEGER);
    $stmt->execute();

    header('Location: /admin/streams/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stream - VidiQ</title>
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
                <li><a href="/admin/streams/index.php" class="active">Streams</a></li>
                <li><a href="/admin/reseller/index.php">Resellers</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Add Stream</h1>
            <form method="POST">
                <label for="name">Stream Name</label>
                <input type="text" id="name" name="name" required>

                <label for="source">Stream Source</label>
                <input type="text" id="source" name="source" required>

                <label for="status">
                    <input type="checkbox" id="status" name="status"> Active
                </label>

                <button type="submit" class="button">Add Stream</button>
            </form>
        </div>
    </main>
</body>
</html>
