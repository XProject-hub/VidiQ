<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/users/index.php');
    exit;
}

$userId = $_GET['id'];

// Fetch user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    header('Location: /admin/users/index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $updateStmt = $db->prepare("UPDATE users SET username = :username" . (!empty($password) ? ", password = :password" : "") . " WHERE id = :id");
    $updateStmt->bindValue(':username', $username, SQLITE3_TEXT);
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateStmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
    }
    $updateStmt->bindValue(':id', $userId, SQLITE3_INTEGER);
    $updateStmt->execute();

    header('Location: /admin/users/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - VidiQ</title>
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
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Edit User</h1>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

                <label for="password">New Password (leave blank to keep current)</label>
                <input type="password" id="password" name="password">

                <button type="submit" class="button">Save Changes</button>
            </form>
        </div>
    </main>
</body>
</html>
