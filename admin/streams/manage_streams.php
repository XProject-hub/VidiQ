<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// Ensure only admin users can access this page.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'admin';

// Include database configuration (config file is two levels up from streams/)
require_once __DIR__ . '/../../config/config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Database connection error: " . $mysqli->connect_error);
}

// Query the streams table
// Adjust column names as per your schema; here we alias stream_url as source.
$query = "SELECT id, icon, name, stream_url AS source, clients, uptime, player, epg, stream_info FROM streams ORDER BY name ASC";
$result = $mysqli->query($query);
if (!$result) {
    die("Query error: " . $mysqli->error);
}
$channels = [];
while ($row = $result->fetch_assoc()) {
    $channels[] = $row;
}
$result->free();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Streams - VidiQ Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Link to external CSS for overall admin styling -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">
  <!-- Additional inline styles for manage_streams page -->
  <style>
    /* Full-width container for channels management */
    .channels-container {
      background: rgba(0, 0, 0, 0.45);
      backdrop-filter: blur(6px);
      padding: 20px;
      border-radius: 8px;
      margin: 100px 0 80px;
      width: 100%;
    }
    .channels-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #00bcd4;
      font-size: 1.8rem;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: rgba(31, 31, 31, 0.8);
      color: #f0f0f0;
      margin-bottom: 20px;
    }
    table th, table td {
      padding: 12px 15px;
      border: 1px solid #333;
      text-align: left;
      vertical-align: middle;
    }
    table th {
      background-color: rgba(0, 188, 212, 0.8);
      color: #fff;
      font-size: 1rem;
    }
    table tr:nth-child(even) {
      background-color: rgba(31, 31, 31, 0.5);
    }
    /* Action buttons */
    .actions a {
      padding: 6px 10px;
      border-radius: 4px;
      background-color: #ff4081;
      color: #fff;
      margin-right: 5px;
      transition: background 0.3s ease;
      font-size: 0.9rem;
    }
    .actions a:hover {
      background-color: #e73370;
    }
    /* Icon styling */
    .channel-icon {
      height: 40px;
      width: auto;
    }
    /* Add Channel Button */
    .add-channel {
      display: inline-block;
      margin-bottom: 15px;
      padding: 10px 18px;
      background-color: #00bcd4;
      color: #fff;
      border-radius: 4px;
      transition: background 0.3s ease;
      font-size: 1rem;
    }
    .add-channel:hover {
      background-color: #0097a7;
    }
  </style>
</head>
<body>
  <!-- HEADER: Standalone header for manage_streams.php -->
  <header class="header">
    <div class="left-section">
      <div class="logo">
        <img src="/admin/assets/images/logo.png" alt="VidiQ Logo">
      </div>
      <!-- Include navigation from navigation.php (one level up from streams/) -->
      <?php include __DIR__ . '/../navigation.php'; ?>
    </div>
    <div class="user-info">
      <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
      <ul class="dropdown">
        <li><a href="/admin/profile.php">Profile</a></li>
        <li><a href="/admin/account.php">Account</a></li>
        <li><a href="/admin/logout.php">Logout</a></li>
      </ul>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="channels-container">
      <h2>Manage Streams</h2>
      <a class="add-channel" href="/admin/streams/add_stream.php">+ Add New Stream</a>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Icon</th>
            <th>Name</th>
            <th>Source</th>
            <th>Clients</th>
            <th>Uptime</th>
            <th>Actions</th>
            <th>Player</th>
            <th>EPG</th>
            <th>Stream Info</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($channels)): ?>
            <?php foreach ($channels as $channel): ?>
              <tr>
                <td><?php echo htmlspecialchars($channel['id']); ?></td>
                <td>
                  <?php if (!empty($channel['icon'])): ?>
                    <img src="<?php echo htmlspecialchars($channel['icon']); ?>" alt="Icon" class="channel-icon">
                  <?php else: ?>
                    <img src="/admin/assets/images/placeholder_icon.png" alt="Icon" class="channel-icon">
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($channel['name']); ?></td>
                <td><?php echo htmlspecialchars($channel['source']); ?></td>
                <td><?php echo htmlspecialchars($channel['clients'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($channel['uptime']); ?></td>
                <td class="actions">
                  <a href="/admin/streams/edit_stream.php?id=<?php echo $channel['id']; ?>">Edit</a>
                  <a href="/admin/streams/delete_stream.php?id=<?php echo $channel['id']; ?>" onclick="return confirm('Are you sure you want to delete this stream?');">Delete</a>
                </td>
                <td><?php echo htmlspecialchars($channel['player']); ?></td>
                <td><?php echo htmlspecialchars($channel['epg']); ?></td>
                <td><?php echo htmlspecialchars($channel['stream_info']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" style="text-align:center;">No streams found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- FOOTER -->
  <?php
  // Use footer.php if available; if not, output a fallback footer.
  if (file_exists(__DIR__ . '/../footer.php')) {
      include __DIR__ . '/../footer.php';
  } else {
      echo '<footer class="footer"><p>&copy; ' . date("Y") . ' VidiQ. All rights reserved.</p></footer>';
  }
  ?>
</body>
</html>
