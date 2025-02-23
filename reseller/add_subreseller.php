<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'reseller') {
    header("Location: /public/index.php");
    exit;
}
require_once __DIR__ . '/../config/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSubUser = trim($_POST['username'] ?? '');
    $newSubPass = trim($_POST['password'] ?? '');
    if ($newSubUser && $newSubPass) {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            $message = "Database connection error: " . $mysqli->connect_error;
        } else {
            $stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, MD5(?), 'subreseller')");
            if ($stmt) {
                $stmt->bind_param("ss", $newSubUser, $newSubPass);
                if ($stmt->execute()) {
                    $message = "New subreseller added successfully.";
                } else {
                    $message = "Error adding subreseller: " . $mysqli->error;
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
    <title>Add Subreseller - VidiQ</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Add Subreseller</h1>
        <nav>
            <a href="/reseller/dashboard.php">Back to Dashboard</a>
        </nav>
    </header>
    <main>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <div>
                <label for="username">Subreseller Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Subreseller Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Add Subreseller</button>
        </form>
    </main>
</body>
</html>
