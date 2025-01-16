<?php
session_start();
$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Create users table if not exists
$db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");

// Check if admin user exists
$result = $db->querySingle("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
if ($result == 0) {
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $db->exec("INSERT INTO users (username, password) VALUES ('admin', '$hashedPassword')");
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VidiQ Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1b1b1b;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #2c2c2c;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 0px 10px #00ffff;
            width: 300px;
        }
        .login-container h1 {
            text-align: center;
            color: #00ffff;
        }
        .login-container input[type="text"], 
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #008b8b;
        }
        .error {
            color: #ff4d4d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>VidiQ Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
