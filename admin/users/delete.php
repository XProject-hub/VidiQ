<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home//Vidiq/config/auto.db');

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/users/index.php');
    exit;
}

$userId = $_GET['id'];

// Delete the user
$stmt = $db->prepare("DELETE FROM users WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$stmt->execute();

header('Location: /admin/users/index.php');
exit;
