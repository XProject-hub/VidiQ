<?php
// index.php

// If the form is submitted, handle login logic here.
// In production, you would verify username/password against a database,
// set sessions, redirect, etc.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Dummy check (replace with real authentication)
    if ($username === 'admin' && $password === 'admin') {
        // Example: redirect to dashboard or set session
        header('Location: dashboard.php');
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
    <title>VidiQ Cool Login</title>
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
    <!-- Logo at the top -->
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
