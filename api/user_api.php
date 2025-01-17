<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch users
        $query = "SELECT id, username, email, created_at FROM users";
        $result = $db->query($query);
        echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        // Add new user
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$data['username'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]);
        echo json_encode(['status' => 'success']);
        break;

    case 'PUT':
        // Update user
        parse_str(file_get_contents('php://input'), $_PUT);
        $stmt = $db->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $stmt->execute([$_PUT['email'], password_hash($_PUT['password'], PASSWORD_BCRYPT), $_PUT['id']]);
        echo json_encode(['status' => 'success']);
        break;

    case 'DELETE':
        // Delete user
        parse_str(file_get_contents('php://input'), $_DELETE);
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_DELETE['id']]);
        echo json_encode(['status' => 'success']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
