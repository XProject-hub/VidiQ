<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /public/index.php");
    exit;
}
require_once __DIR__ . '/../config/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newResellerUser = trim($_POST['username'] ?? '');
    $newResellerPass = trim($_POST['password'] ?? '');
    if ($newResellerUser && $newResellerPass) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            $message = "Database connection error: " . $mysqli->connect_error;
        } else {
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, MD5(?), 'reseller')");
            if ($stmt) {
                $stmt->bind_param("ss", $newResellerUser, $newResellerPass);
                if ($stmt->execute()) {
                    $message = "New reseller added successfully.";
                } else {
                    $message = "Error adding reseller: " . $mysqli->error;
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
    <title>Manage Resellers - VidiQ</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Manage Resellers</h1>
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
                <label for="username">New Reseller Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">New Reseller Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Add Reseller</button>
        </form>
    </main>
</body>
</html>
