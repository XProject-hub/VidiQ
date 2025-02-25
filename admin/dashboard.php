<?php
session_start();
// Restrict access to admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VidiQ Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- External CSS file for styling -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">
</head>
<body>
  <!-- HEADER with horizontal navigation and user dropdown -->
  <header class="header">
    <div class="left-section">
      <div class="logo">
        <!-- Logo from /admin/assets/images/logo.png -->
        <img src="/admin/assets/images/logo.png" alt="VidiQ Logo">
      </div>
      <nav class="nav-links">
        <ul>
          <li><a href="/admin/dashboard.php">Dashboard</a></li>
          <li><a href="#">Servers</a></li>
          <li><a href="#">Management</a></li>
          <li><a href="#">Users</a></li>
          <li><a href="#">Settings</a></li>
          <li><a href="#">Logs</a></li>
        </ul>
      </nav>
    </div>
    <div class="user-info">
      <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
      <ul class="dropdown">
        <li><a href="#">Profile</a></li>
        <li><a href="#">Account</a></li>
        <li><a href="#">Logout</a></li>
      </ul>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <div class="widgets">
      <div class="widget">
        <h2>Live Connections</h2>
        <p>12 active streams</p>
      </div>
      <div class="widget">
        <h2>Server Status</h2>
        <p>All systems operational</p>
      </div>
      <div class="widget">
        <h2>Activity Log</h2>
        <p>Recent activity displayed here</p>
      </div>
    </div>
  </div>

  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> VidiQ. All rights reserved.</p>
  </footer>
</body>
</html>
