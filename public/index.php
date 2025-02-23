<?php
session_start();
require_once __DIR__ . '/../config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        $error = "Database connection error: " . $mysqli->connect_error;
    } else {
        // First, check the admin table
        $stmt = $mysqli->prepare("SELECT id, username FROM admin WHERE username = ? AND password = MD5(?)");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'admin';
                header("Location: /admin/dashboard.php");
                exit;
            }
            $stmt->close();
        }
        // If not found in admin table, check the users table
        $stmt = $mysqli->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = MD5(?)");
        if ($stmt) {
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                if ($user['role'] === 'reseller') {
                    header("Location: /reseller/dashboard.php");
                    exit;
                } elseif ($user['role'] === 'subreseller') {
                    header("Location: /subreseller/dashboard.php");
                    exit;
                } else {
                    $error = "Invalid role.";
                }
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        } else {
            $error = "Database query error.";
        }
        $mysqli->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>VidiQ Login</title>
    <link rel="stylesheet" href="/assets/css/style.css" />
    <style>
        body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; }
        .login-container { width: 400px; margin: 100px auto; padding: 20px; background-color: #1e1e1e; border-radius: 5px; }
        .login-container h1 { text-align: center; color: #00ffff; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #333; background-color: #2c2c2c; color: #e0e0e0; }
        .login-btn { width: 100%; padding: 10px; background-color: #00ffff; border: none; color: #121212; font-weight: bold; cursor: pointer; }
        .error { background-color: #ff4d4d; padding: 10px; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="/assets/images/logo.png" alt="VidiQ Logo" style="display:block; margin: 0 auto 20px auto;" />
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required />
            </div>
            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
</body>
</html>
