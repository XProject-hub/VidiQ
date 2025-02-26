<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VidiQ Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="/admin/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/navigation.php'; ?>
  <main class="main-content">
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
  </main>
  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> VidiQ. All rights reserved.</p>
  </footer>
</body>
</html>
