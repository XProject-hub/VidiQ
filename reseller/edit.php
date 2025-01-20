<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home//Vidiq/config/auto.db');

// Check if reseller ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/reseller/index.php');
    exit;
}

$resellerId = $_GET['id'];

// Fetch reseller details
$stmt = $db->prepare("SELECT * FROM resellers WHERE id = :id");
$stmt->bindValue(':id', $resellerId, SQLITE3_INTEGER);
$result = $stmt->execute();
$reseller = $result->fetchArray(SQLITE3_ASSOC);

if (!$reseller) {
    header('Location: /admin/reseller/index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $updateStmt = $db->prepare("UPDATE resellers SET username = :username, email = :email WHERE id = :id");
    $updateStmt->bindValue(':username', $username, SQLITE3_TEXT);
    $updateStmt->bindValue(':email', $email, SQLITE3_TEXT);
    $updateStmt->bindValue(':id', $resellerId, SQLITE3_INTEGER);
    $updateStmt->execute();

    header('Location: /admin/reseller/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reseller - VidiQ</title>
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
            <h1>Edit Reseller</h1>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($reseller['username']) ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($reseller['email']) ?>" required>

                <button type="submit" class="button">Save Changes</button>
            </form>
        </div>
    </main>
</body>
</html>
