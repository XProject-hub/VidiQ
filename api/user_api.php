<?php
require_once '../config/database.php'; // Database configuration file

header('Content-Type: application/json');

// Fetch the request method
$method = $_SERVER['REQUEST_METHOD'];

try {
    // Ensure database connection exists
    if (!isset($db)) {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    switch ($method) {
        case 'GET': // Fetch all users
            $query = "SELECT id, username, email, role, created_at FROM users";
            $stmt = $db->query($query);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $users]);
            break;

        case 'POST': // Add a new user
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
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

        case 'PUT': // Update an existing user
            parse_str(file_get_contents('php://input'), $_PUT);

            // Validate required fields
            if (empty($_PUT['id']) || empty($_PUT['email']) || empty($_PUT['password']) || empty($_PUT['role'])) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
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
            parse_str(file_get_contents('php://input'), $_DELETE);

            if (empty($_DELETE['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
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
