<?php
// /admin/streams/manage_streams.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once("../../config/config.php");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM streams");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Streams - VidiQ</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; }
    .container { width: 800px; margin: 50px auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border: 1px solid #333; text-align: left; }
    th { background-color: #1e1e1e; }
    a { color: #00ffff; text-decoration: none; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Manage Streams</h1>
    <table>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>URL</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['stream_url']); ?></td>
        <td><?php echo htmlspecialchars($row['category']); ?></td>
        <td>
          <!-- Action: Preview stream -->
          <a href="preview_stream.php?id=<?php echo $row['id']; ?>">Preview</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
    <p><a href="add_stream.php">Add New Stream</a></p>
  </div>
</body>
</html>
<?php
$mysqli->close();
?>
