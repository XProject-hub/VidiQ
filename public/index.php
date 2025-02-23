<?php
session_start();
require_once __DIR__ . '/../config/config.php'; // Adjust path if needed

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Dummy authentication logic: replace with your real authentication
    if ($username === 'admin' && $password === 'admin') {
        // For demonstration, set a session variable and redirect to dashboard
        $_SESSION['user'] = 'admin';
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>VidiQ Login</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="login-container">
        <img src="assets/images/logo.png" alt="VidiQ Logo" class="logo" />
        <h1>VidiQ Login</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter username" 
                    required 
                />
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter password" 
                    required 
                />
            </div>
            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
</body>
</html>
