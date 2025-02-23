<?php
// index.php
session_start();
require_once __DIR__ . '/../config/config.php'; // Adjust path if needed

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        // Connect to database using credentials from config.php
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            $error = "Database connection error: " . $mysqli->connect_error;
        } else {
            // Use prepared statement to protect against SQL injection
            $stmt = $mysqli->prepare("SELECT id, username, role FROM admin WHERE username = ? AND password = MD5(?)");
            if ($stmt) {
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on user role
                    if ($user['role'] === 'admin') {
                        header("Location: /admin/dashboard.php");
                        exit;
                    } elseif ($user['role'] === 'reseller') {
                        header("Location: /reseller/dashboard.php");
                        exit;
                    } elseif ($user['role'] === 'subreseller') {
                        header("Location: /subreseller/dashboard.php");
                        exit;
                    } else {
                        $error = "Your account does not have a valid role.";
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>VidiQ Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Open Sans", sans-serif;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #3F51B5, #2196F3);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background-color: #fff;
            width: 350px;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
            text-align: center;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 1rem;
        }
        .login-container h1 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 600;
        }
        .input-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #555;
        }
        .input-group input {
            width: 100%;
            padding: 0.6rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login-btn {
            width: 100%;
            padding: 0.7rem;
            background: #3F51B5;
            color: #fff;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login-btn:hover {
            background: #303f9f;
        }
        .error {
            color: #f44336;
            margin-bottom: 1rem;
        }
    </style>
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
