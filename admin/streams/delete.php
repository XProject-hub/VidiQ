<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}

$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if stream ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM streams WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
}

header('Location: /admin/streams/index.php');
exit;
?>
