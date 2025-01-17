<?php
header('Content-Type: application/json');

// Load main server details
$configPath = __DIR__ . '/../config/main_server.json';
if (file_exists($configPath)) {
    $mainServer = json_decode(file_get_contents($configPath), true);

    // Fetch real-time stats
    $cpuUsage = shell_exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'");
    $ramUsage = shell_exec("free | awk '/Mem/ {print $3/$2 * 100.0}'");
    $connections = shell_exec("netstat -an | grep ESTABLISHED | wc -l");

    echo json_encode([
        'ip' => $mainServer['ip'],
        'name' => $mainServer['name'],
        'cpu' => round($cpuUsage, 2),
        'ram' => round($ramUsage, 2),
        'connections' => intval($connections),
        'streamsLive' => rand(10, 50), // Mock data
        'streamsOff' => rand(1, 10), // Mock data
        'input' => rand(1000, 5000), // Mock data in Mbps
        'output' => rand(1000, 5000) // Mock data in Mbps
    ]);
} else {
    echo json_encode(['error' => 'Main server information not found.']);
}
?>
