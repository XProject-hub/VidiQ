<?php
// Database configuration
define('DB_FILE', __DIR__ . '/../database/database.sqlite');

try {
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Hashing salt configuration
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
