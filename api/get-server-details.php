<?php

// Enable CORS if the API is accessed from another domain
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Database connection configuration using environment variables
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'your_database_name';
    $username = $_ENV['DB_USER'] ?? 'your_database_username';
    $password = $_ENV['DB_PASSWORD'] ?? 'your_database_password';

    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch server details
    $query = "
        SELECT 
            server_name,
            connections,
            live_streams 
        FROM 
            server_details 
        WHERE 
            server_name = :server_name"; // Use named parameter for better security

    $stmt = $pdo->prepare($query);
    $stmt->execute([':server_name' => 'Main Server']); // Bind the parameter value

    // Fetch the data
    $serverDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($serverDetails) {
        // Return the data as JSON
        echo json_encode([
            "status" => "success",
            "connections" => $serverDetails["connections"],
            "liveStreams" => $serverDetails["live_streams"]
        ]);
    } else {
        // Return an error if no data is found
        echo json_encode([
            "status" => "error",
            "message" => "No server details found."
        ]);
    }

} catch (PDOException $e) {
    // Handle database errors
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
    // Optionally, log the error for debugging purposes
    error_log("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    // Handle other errors
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred: " . $e->getMessage()
    ]);
    // Optionally, log the error for debugging purposes
    error_log("General Error: " . $e->getMessage());
}
