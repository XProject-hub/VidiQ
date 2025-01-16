<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user is an admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/dashboard/index.php");
        exit;
    }

    // Check if the user is a reseller
    $stmt = $conn->prepare("SELECT * FROM resellers WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $reseller = $stmt->get_result()->fetch_assoc();

    if ($reseller && password_verify($password, $reseller['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['role'] = 'reseller';
        header("Location: ../reseller/dashboard/index.php");
        exit;
    }

    $error = "Invalid login credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VidiQ Login</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="login-form">
        <img src="images/VidiQ_Logo.png" alt="VidiQ Logo">
        <h1>Login</h1>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
