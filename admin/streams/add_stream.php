<?php
// /admin/streams/add_stream.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once("../../config/config.php");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $stream_url = trim($_POST['stream_url']);
    $category = trim($_POST['category']);
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    $stmt = $mysqli->prepare("INSERT INTO streams (name, stream_url, category) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $stream_url, $category);
    if ($stmt->execute()) {
        $message = "Stream added successfully.";
    } else {
        $message = "Error adding stream: " . $mysqli->error;
    }
    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Stream - VidiQ</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; }
    .container { width: 600px; margin: 50px auto; }
    input[type=text], input[type=url] { width: 100%; padding: 10px; margin: 5px 0; background-color: #2c2c2c; border: 1px solid #333; color: #e0e0e0; }
    input[type=submit] { padding: 10px; background-color: #00ffff; border: none; color: #121212; font-weight: bold; cursor: pointer; }
    .message { color: #00ff00; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Add New Stream</h1>
    <?php if ($message != ""): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="add_stream.php">
      <label for="name">Stream Name:</label>
      <input type="text" name="name" required>
      <label for="stream_url">Stream URL:</label>
      <input type="url" name="stream_url" required>
      <label for="category">Category:</label>
      <input type="text" name="category">
      <input type="submit" value="Add Stream">
    </form>
    <p><a href="manage_streams.php">Manage Streams</a></p>
  </div>
</body>
</html>
