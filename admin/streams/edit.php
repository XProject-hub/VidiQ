<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if stream ID is provided
if (!isset($_GET['id'])) {
    header('Location: /admin/streams/index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM streams WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$stream = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$stream) {
    header('Location: /admin/streams/index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $source = $_POST['source'];
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $db->prepare("UPDATE streams SET name = :name, source = :source, status = :status WHERE id = :id");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':source', $source, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_INTEGER);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
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
    <title>Edit Stream - VidiQ</title>
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
            <h1>Edit Stream</h1>
            <form method="POST">
                <label for="name">Stream Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($stream['name']) ?>" required>

                <label for="source">Stream Source</label>
                <input type="text" id="source" name="source" value="<?= htmlspecialchars($stream['source']) ?>" required>

                <label for="status">
                    <input type="checkbox" id="status" name="status" <?= $stream['status'] == 1 ? 'checked' : '' ?>> Active
                </label>

                <button type="submit" class="button">Update Stream</button>
            </form>
        </div>
    </main>
</body>
</html>
