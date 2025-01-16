<?php
include '../config/database.php';

// Function to add a new DRM key
function addDRMKey($kid, $key) {
    global $conn;
    $sql = "INSERT INTO drm_keys (kid, key) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $kid, $key);
    $stmt->execute();
    return $stmt->insert_id;
}

// Function to fetch a DRM key
function getDRMKey($kid) {
    global $conn;
    $sql = "SELECT key FROM drm_keys WHERE kid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['key'];
    }
    return null; // Key not found
}
?>
