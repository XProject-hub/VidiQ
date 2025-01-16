<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Placeholder for password verification, adjust as needed
    $sql = "SELECT * FROM admins WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['loggedin'] = true;
        header("Location: admin/dashboard/index.php");
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VidiQ Login</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #121212; /* Dark background */
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .login-form {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #00bcd4; /* Cyan border */
            border-radius: 5px;
        }

        button {
            background-color: #00bcd4; /* Cyan */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0097a7;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <img src="images/VidiQ_Logo.png" alt="VidiQ Logo" style="width: 200px; animation: rotate 5s linear infinite;">
        <h1>Login</h1>
        <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        const logo = document.querySelector('img');
        let angle = 0;
        setInterval(() => {
            angle += 2;
            logo.style.transform = `rotate(${angle}deg)`;
        }, 100);
    </script>
</body>
</html>
