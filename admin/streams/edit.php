<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if stream ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/streams/index.php');
    exit;
}

$streamId = $_GET['id'];

// Fetch stream details
$stmt = $db->prepare("SELECT * FROM streams WHERE id = :id");
$stmt->bindValue(':id', $streamId, SQLITE3_INTEGER);
$result = $stmt->execute();
$stream = $result->fetchArray(SQLITE3_ASSOC);

if (!$stream) {
    header('Location: /admin/streams/index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $url = $_POST['url'];

    $updateStmt = $db->prepare("UPDATE streams SET name = :name, url = :url WHERE id = :id");
    $updateStmt->bindValue(':name', $name, SQLITE3_TEXT);
    $updateStmt->bindValue(':url', $url, SQLITE3_TEXT);
    $updateStmt->bindValue(':id', $streamId, SQLITE3_INTEGER);
    $updateStmt->execute();

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
                <li><a href="/admin/dashboard/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/users/index.php">Users</a></li>
                <li><a href="/admin/streams/index.php" class="active">Streams</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Edit Stream</h1>
            <form method="POST">
                <label for="name">Stream Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($stream['name']) ?>" required>

                <label for="url">Stream URL</label>
                <input type="url" id="url" name="url" value="<?= htmlspecialchars($stream['url']) ?>" required>

                <button type="submit" class="button">Save Changes</button>
            </form>
        </div>
    </main>
</body>
</html>
