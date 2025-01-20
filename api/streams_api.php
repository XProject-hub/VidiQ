<?php
require_once '../config/session_manager.php';

// Restrict API access to Admin and Editor roles
checkRole('Admin', 'Editor');

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            $query = "SELECT * FROM streams";
            $stmt = $db->query($query);
            $streams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $streams]);
            break;

        case 'POST':
            // Validate and sanitize input
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['name']) || !isset($data['category'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
                return;
            }

            $stmt = $db->prepare("INSERT INTO streams (name, category) VALUES (?, ?)");
            if ($stmt->execute([$data['name'], $data['category']])) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database insert error']);
            }
            break;

        case 'PUT':
            // Validate and sanitize input
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['id']) || !isset($data['name']) || !isset($data['category'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
                return;
            }

            $stmt = $db->prepare("UPDATE streams SET name = ?, category = ? WHERE id = ?");
            if ($stmt->execute([$data['name'], $data['category'], $data['id']])) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database update error']);
            }
            break;

        case 'DELETE':
            // Validate and sanitize input
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
                return;
            }

            $stmt = $db->prepare("DELETE FROM streams WHERE id = ?");
            if ($stmt->execute([$data['id']])) {
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database delete error']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    // Log the error for debugging purposes
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
}
?>
