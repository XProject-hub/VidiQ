<?php
// /admin/dashboard.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - VidiQ</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Additional styles can be added in assets/css/style.css */
    body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; }
    header { background-color: #1e1e1e; padding: 10px; }
    nav ul { list-style-type: none; padding: 0; }
    nav ul li { display: inline-block; position: relative; margin-right: 20px; }
    nav ul li ul { display: none; position: absolute; background: #1e1e1e; padding: 10px; }
    nav ul li:hover ul { display: block; }
    nav a { color: #00ffff; text-decoration: none; }
  </style>
</head>
<body>
  <header>
    <?php include 'navigation.php'; ?>
  </header>
  <main>
    <h1>Welcome, Admin</h1>
    <p>Dashboard Overview:</p>
    <ul>
      <li>Live Connections</li>
      <li>Activity Log</li>
      <li>Process Monitor</li>
    </ul>
  </main>
</body>
</html>
