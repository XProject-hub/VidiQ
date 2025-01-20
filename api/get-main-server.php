<?php
header('Content-Type: application/json');

// Load main server details
$configPath = __DIR__ . '/../config/main_server.json';
if (file_exists($configPath)) {
    $mainServer = json_decode(file_get_contents($configPath), true);

    // Fetch real-time stats using safer methods if possible

    // CPU Usage - using system commands safely
    exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'", $output, $returnVar);
    $cpuUsage = isset($output[0]) ? floatval($output[0]) : 0;

    // RAM Usage - using system commands safely
    exec("free | awk '/Mem/ {print $3/$2 * 100.0}'", $output, $returnVar);
    $ramUsage = isset($output[0]) ? floatval($output[0]) : 0;

    // Connections - using safer method
    exec("netstat -an | grep ESTABLISHED | wc -l", $connectionsOutput, $returnVar);
    $connections = isset($connectionsOutput[0]) ? intval($connectionsOutput[0]) : 0;

    echo json_encode([
        'ip' => $mainServer['ip'],
        'name' => $mainServer['name'],
        'cpu' => round($cpuUsage, 2),
        'ram' => round($ramUsage, 2),
        'connections' => intval($connections),
        // Mock data
        'streamsLive' => rand(10, 50), 
        'streamsOff' => rand(1, 10),
        'input' => rand(1000, 5000), // in Mbps
        'output' => rand(1000, 5000) // in Mbps
    ]);
} else {
    echo json_encode(['error' => 'Main server information not found.']);
}
?>
