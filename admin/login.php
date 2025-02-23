<?php
// /admin/login.php
session_start();
require_once("../config/config.php");

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare("SELECT id, username, password, role FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // If user exists then verify password
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();
        // Using password_verify for hashed password (make sure to hash passwords when inserting new users)
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VidiQ Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      /* Inline basic dark/cyan style; ideally in your css file */
      body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; }
      .login-container { width: 300px; margin: 100px auto; padding: 20px; background-color: #1e1e1e; border-radius: 5px; }
      .login-container h1 { text-align: center; color: #00ffff; }
      input[type=text], input[type=password] { width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #333; background-color: #2c2c2c; color: #e0e0e0; }
      input[type=submit] { width: 100%; padding: 10px; background-color: #00ffff; border: none; color: #121212; font-weight: bold; cursor: pointer; }
      .error { color: #ff4d4d; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>VidiQ Panel</h1>
        <?php if(isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
