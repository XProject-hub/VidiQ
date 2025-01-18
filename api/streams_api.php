<?php
require_once '../config/session_manager.php';

// Restrict API access to Admin and Editor roles
checkRole('Editor');

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $query = "SELECT * FROM streams";
            $stmt = $db->query($query);
            $streams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($streams);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("INSERT INTO streams (name, category) VALUES (?, ?)");
            $stmt->execute([$data['name'], $data['category']]);
            echo json_encode(['status' => 'success']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("UPDATE streams SET name = ?, category = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['category'], $data['id']]);
            echo json_encode(['status' => 'success']);
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("DELETE FROM streams WHERE id = ?");
            $stmt->execute([$data['id']]);
            echo json_encode(['status' => 'success']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
