<?php
// Enable CORS if the API is accessed from another domain
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection configuration
$host = "localhost";
$dbname = "your_database_name";
$username = "your_database_username";
$password = "your_database_password";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
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
            server_name = 'Main Server'"; // Adjust to your server naming logic

    $stmt = $pdo->prepare($query);
    $stmt->execute();

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
} catch (Exception $e) {
    // Handle other errors
    echo json_encode([
        "status" => "error",
        "message" => "An error occurred: " . $e->getMessage()
    ]);
}
