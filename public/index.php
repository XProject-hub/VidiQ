<?php
session_start();
try {
    $db = new SQLite3('/home/Vidiq/config/auto.db');
} catch (Exception $e) {
    die('Database error: ' . $e->getMessage());
}

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
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        header('Location: ../admin/dashboard.php');
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
            overflow: hidden;
        }

        .login-container {
            background-color: #2c2c2c;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0px 0px 30px #00ffff;
            width: 400px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        .login-container img {
            width: 150px;
            margin-bottom: 20px;
        }

        .login-container input[type="text"], 
        .login-container input[type="password"] {
            width: calc(100% - 32px);
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #444;
            border-radius: 6px;
            background-color: #333;
            color: #fff;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 6px;
            background-color: #00ffff;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .login-container button:hover {
            background-color: #008b8b;
            transform: scale(1.05);
        }

        .error {
            color: #ff4d4d;
            margin-bottom: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="/images/logo.png" alt="VidiQ Logo">
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
