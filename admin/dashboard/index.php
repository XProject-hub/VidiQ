<?php
include '../../../config/system.php';
include '../../../config/database.php';

// Fetch server and streaming statistics
function fetchStatistics() {
    global $conn;
    // Example query, adjust based on your actual database schema
    $stats = [
        'clients' => $conn->query("SELECT COUNT(*) FROM clients")->fetch_row()[0],
        'channels' => $conn->query("SELECT COUNT(*) FROM streams")->fetch_row()[0],
        'movies' => $conn->query("SELECT COUNT(*) FROM movies")->fetch_row()[0],
        'series' => $conn->query("SELECT COUNT(*) FROM series")->fetch_row()[0]
    ];
    return $stats;
}

$stats = fetchStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../public/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
</head>
<body>
    <header>
        <img src="../../../public/images/VidiQ_Logo.png" alt="VidiQ Logo" style="height: 50px;">
        <h1>Welcome to VidiQ Admin Dashboard</h1>
    </header>
    <section>
        <h2>Statistics</h2>
        <p>Total Clients: <?= $stats['clients']; ?></p>
        <p>Total Channels: <?= $stats['channels']; ?></p>
        <p>Total Movies: <?= $stats['movies']; ?></p>
        <p>Total Series: <?= $stats['series']; ?></p>
    </section>
    <section>
        <h2>Global Usage Map</h2>
        <canvas id="worldMap" width="400" height="200"></canvas>
        <script>
            var ctx = document.getElementById('worldMap').getContext('2d');
            var worldMap = new Chart(ctx, {
                // Chart configuration for a world map, use a suitable library or API for geographic data visualization
            });
        </script>
    </section>
</body>
</html>
