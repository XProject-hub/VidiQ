<?php
// /public/update_server_stats.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If you want to restrict access, consider a token or IP check here
// e.g. if($_GET['token'] !== 'secret') { die('Unauthorized'); }

// Include your config for DB
require_once __DIR__ . '/../config/config.php';

// Connect to DB
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("DB connection error: " . $mysqli->connect_error);
}

// 1) CPU Usage (Naive approach: parse top)
$cpu_usage = 0;
$topOutput = shell_exec("top -b -n 1 | grep 'Cpu(s)'");
if ($topOutput) {
    // e.g. "Cpu(s): 10.3%us,  2.3%sy, ..."
    if (preg_match('/(\d+(\.\d+)?)%us/', $topOutput, $matches)) {
        $cpu_usage = round($matches[1]);
    }
}

// 2) RAM Usage (Naive approach: parse free -m)
$ram_usage = 0;
$freeOutput = shell_exec("free -m");
if ($freeOutput) {
    $lines = explode("\n", trim($freeOutput));
    if (count($lines) >= 2) {
        $memLine = preg_split('/\s+/', $lines[1]);
        // total, used, free...
        $totalMem = (int)$memLine[1];
        $usedMem  = (int)$memLine[2];
        if ($totalMem > 0) {
            $ram_usage = round(($usedMem / $totalMem) * 100);
        }
    }
}

// 3) Bandwidth usage (Naive approach - placeholder)
$bandwidth_usage = 0; // real-time net usage requires comparing /proc/net/dev counters over time

// 4) Uptime
$uptime = "N/A";
$uptimeOut = shell_exec("uptime -p"); // e.g. "up 4 days, 8 hours"
if ($uptimeOut) {
    $uptime = trim(str_replace("up ", "", $uptimeOut));
}

// 5) Users, live connections, down channels (placeholder)
$users           = 2; // you might read from DB: SELECT COUNT(*) FROM users
$live_connections = 5; 
$down_channels   = 1; 

// Update the 'servers' table for the main server (is_main=1)
$stmt = $mysqli->prepare("
  UPDATE servers 
  SET cpu_usage=?, ram_usage=?, bandwidth_usage=?, users=?, live_connections=?, down_channels=?, uptime=?
  WHERE is_main=1
");
if ($stmt) {
    $stmt->bind_param("iiiiiis", 
        $cpu_usage, $ram_usage, $bandwidth_usage, 
        $users, $live_connections, $down_channels, $uptime
    );
    $stmt->execute();
    $stmt->close();
}

$mysqli->close();

echo "Server stats updated successfully.\n";
