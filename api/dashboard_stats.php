<?php
require_once '../config/session_manager.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userRole = $_SESSION['role'];

try {
    $stats = [
        'totalUsers' => 0,
        'activeStreams' => 0,
        'cpuUsage' => 0,
        'ramUsage' => 0,
        'diskUsage' => 0,
        'recentLogs' => []
    ];

    // Fetch total users and active streams
    $stmt = $db->query("SELECT COUNT(*) AS totalUsers FROM users");
    if ($stmt) {
        $stats['totalUsers'] = $stmt->fetchColumn();
    } else {
        throw new Exception('Failed to fetch total users');
    }

    $stmt = $db->query("SELECT COUNT(*) AS activeStreams FROM streams WHERE status = 'active'");
    if ($stmt) {
        $stats['activeStreams'] = $stmt->fetchColumn();
    } else {
        throw new Exception('Failed to fetch active streams');
    }

    if ($userRole === 'Admin') {
        // Add admin-specific stats
        $stats['cpuUsage'] = rand(20, 70); // Mocked data
        $stats['ramUsage'] = rand(30, 80); // Mocked data
        $stats['diskUsage'] = rand(10, 90); // Mocked data

        // Fetch recent logs (mocked)
        $stats['recentLogs'] = [
            'User admin logged in',
            'Stream 101 started',
            'Database backup completed'
        ];
    }

    echo json_encode($stats);
} catch (\Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
