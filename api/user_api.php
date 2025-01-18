<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Establish database connection
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fetch users
            $query = "SELECT id, username, email, created_at FROM users";
            $stmt = $db->query($query);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $users]);
            break;

        case 'POST':
            // Add new user
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['username'], $data['email'], $data['password'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
                exit;
            }
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $data['username'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT)
            ]);
            echo json_encode(['status' => 'success', 'message' => 'User created']);
            break;

        case 'PUT':
            // Update user
            parse_str(file_get_contents('php://input'), $_PUT);
            if (!isset($_PUT['id'], $_PUT['email'], $_PUT['password'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
                exit;
            }
            $stmt = $db->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([
                $_PUT['email'],
                password_hash($_PUT['password'], PASSWORD_BCRYPT),
                $_PUT['id']
            ]);
            echo json_encode(['status' => 'success', 'message' => 'User updated']);
            break;

        case 'DELETE':
            // Delete user
            parse_str(file_get_contents('php://input'), $_DELETE);
            if (!isset($_DELETE['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing user ID']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_DELETE['id']]);
            echo json_encode(['status' => 'success', 'message' => 'User deleted']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
