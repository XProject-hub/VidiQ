<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

// Fetch the request method and user's role
$method = $_SERVER['REQUEST_METHOD'];

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Fetch the user's role from the database
$stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$role = $user['role'];

try {
    switch ($method) {
        case 'GET': // Fetch all users
            if ($role === 'Viewer') {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                http_response_code(403);
                exit;
            }

            $query = "SELECT id, username, email, role, created_at FROM users";
            $stmt = $db->query($query);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $users]);
            break;

        case 'POST': // Add a new user
            if ($role !== 'Admin') {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                http_response_code(403);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['username'], $data['email'], $data['password'], $data['role'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
                exit;
            }

            $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'User created successfully']);
            break;

        case 'PUT': // Update user details
            if ($role !== 'Admin') {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                http_response_code(403);
                exit;
            }

            parse_str(file_get_contents('php://input'), $_PUT);
            if (!isset($_PUT['id'], $_PUT['email'], $_PUT['password'], $_PUT['role'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
                exit;
            }

            $stmt = $db->prepare("UPDATE users SET email = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([
                $_PUT['email'],
                password_hash($_PUT['password'], PASSWORD_BCRYPT),
                $_PUT['role'],
                $_PUT['id']
            ]);

            echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
            break;

        case 'DELETE': // Delete a user
            if ($role !== 'Admin') {
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                http_response_code(403);
                exit;
            }

            parse_str(file_get_contents('php://input'), $_DELETE);
            if (!isset($_DELETE['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
                exit;
            }

            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_DELETE['id']]);

            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
