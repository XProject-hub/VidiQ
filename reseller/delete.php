<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

$db = new SQLite3('/home//Vidiq/config/auto.db');

// Check if reseller ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /admin/reseller/index.php');
    exit;
}

$resellerId = $_GET['id'];

// Delete the reseller
$stmt = $db->prepare("DELETE FROM resellers WHERE id = :id");
$stmt->bindValue(':id', $resellerId, SQLITE3_INTEGER);
$stmt->execute();

header('Location: /admin/reseller/index.php');
exit;
