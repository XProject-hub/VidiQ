<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /public/index.php");
    exit;
}
require_once __DIR__ . '/../config/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newAdminUser = trim($_POST['username'] ?? '');
    $newAdminPass = trim($_POST['password'] ?? '');
    if ($newAdminUser && $newAdminPass) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            $message = "Database connection error: " . $mysqli->connect_error;
        } else {
            $stmt = $mysqli->prepare("INSERT INTO admin (username, password) VALUES (?, MD5(?))");
            if ($stmt) {
                $stmt->bind_param("ss", $newAdminUser, $newAdminPass);
                if ($stmt->execute()) {
                    $message = "New admin added successfully.";
                } else {
                    $message = "Error adding admin: " . $mysqli->error;
                }
                $stmt->close();
            } else {
                $message = "Database query error.";
            }
            $mysqli->close();
        }
    } else {
        $message = "Please enter a username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admins - VidiQ</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Manage Admins</h1>
        <nav>
            <a href="/admin/dashboard.php">Back to Dashboard</a>
        </nav>
    </header>
    <main>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <div>
                <label for="username">New Admin Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">New Admin Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Add Admin</button>
        </form>
    </main>
</body>
</html>
