<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if stream ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/streams/index.php');
    exit;
}

$streamId = $_GET['id'];

// Delete the stream
$stmt = $db->prepare("DELETE FROM streams WHERE id = :id");
$stmt->bindValue(':id', $streamId, SQLITE3_INTEGER);
$stmt->execute();

header('Location: /admin/streams/index.php');
exit;
