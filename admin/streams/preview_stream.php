<?php
// /admin/streams/preview_stream.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once("../../config/config.php");

if (!isset($_GET['id'])) {
    die("No stream specified.");
}
$stream_id = intval($_GET['id']);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT name, stream_url FROM streams WHERE id=?");
$stmt->bind_param("i", $stream_id);
$stmt->execute();
$stmt->bind_result($name, $stream_url);
$stmt->fetch();
$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Preview Stream - <?php echo htmlspecialchars($name); ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { background-color: #121212; color: #e0e0e0; font-family: Arial, sans-serif; text-align: center; }
    video { max-width: 90%; margin: 20px auto; }
  </style>
</head>
<body>
  <h1>Preview Stream: <?php echo htmlspecialchars($name); ?></h1>
  <video controls autoplay>
    <source src="<?php echo htmlspecialchars($stream_url); ?>" type="application/x-mpegURL">
    Your browser does not support the video tag.
  </video>
  <p><a href="manage_streams.php">Back to Streams</a></p>
</body>
</html>
