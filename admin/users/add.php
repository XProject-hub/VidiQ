<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->execute();

    header('Location: /admin/users/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - VidiQ</title>
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
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>Add New User</h1>
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="button">Add User</button>
            </form>
        </div>
    </main>
</body>
</html>
