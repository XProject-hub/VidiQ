<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home//Vidiq/config/auto.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $db->prepare("INSERT INTO resellers (name, email, phone, status) VALUES (:name, :email, :phone, :status)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_INTEGER);
    $stmt->execute();

    header('Location: /admin/reseller/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reseller - VidiQ</title>
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
            <h1>Add Reseller</h1>
            <form method="POST">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" required>

                <label for="status">
                    <input type="checkbox" id="status" name="status"> Active
                </label>

                <button type="submit" class="button">Add Reseller</button>
            </form>
        </div>
    </main>
</body>
</html>
