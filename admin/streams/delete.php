<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

// Database connection
$db = new SQLite3('/home/Vidiq/panel/config/auto.db');

// Check if stream ID is provided via GET request
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id !== null) {
    // Prepare and execute the DELETE statement with parameter binding to prevent SQL injection
    $stmt = $db->prepare("DELETE FROM streams WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Stream deleted successfully.";
    } else {
        echo "Failed to delete stream. Please try again later.";
    }
}

// Redirect after processing
header('Location: /admin/streams/index.php');
exit;
?>
