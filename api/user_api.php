<?php
require_once '../config/config.php';
session_start();

// Headers for JSON response
header('Content-Type: application/json');

// Define roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_VIEWER', 'Viewer');

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Fetch the authenticated user's role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$role = $user['role'];

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Only Admin can fetch all users
            if ($role !== ROLE_ADMIN) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                exit;
            }

            $query = "SELECT id, username, email, role, created_at FROM users";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['status' => 'success', 'data' => $users]);
            break;

        case 'POST':
            // Only Admin can create a user
            if ($role !== ROLE_ADMIN) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Validate input fields
            if (!isset($data['username'], $data['email'], $data['password'], $data['role'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->execute([$data['username'], $data['email'], $hashedPassword, $data['role']]);

            echo json_encode(['status' => 'success', 'message' => 'User created successfully']);
            break;

        case 'PUT':
            // Only Admin can update a user
            if ($role !== ROLE_ADMIN) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Validate input fields
            if (!isset($data['id'], $data['email'], $data['password'], $data['role'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ?, role = ? WHERE id = ?");
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->execute([$data['email'], $hashedPassword, $data['role'], $data['id']]);

            echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
            break;

        case 'DELETE':
            // Only Admin can delete a user
            if ($role !== ROLE_ADMIN) {
                http_response_code(403);
                echo json_encode(['status' => 'error', 'message' => 'Access denied']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Validate input fields
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$data['id']]);

            echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} catch (PDOException $e) {
    // Log error and return generic error message
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
}
?>
