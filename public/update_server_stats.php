<?php
// /public/update_server_stats.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB config
require_once __DIR__ . '/../config/config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("DB connection error: " . $mysqli->connect_error);
}

// 1) Get public IP using an external service or fallback
$ip = trim(@file_get_contents("https://api.ipify.org"));
if (!$ip) {
    // Fallback if the external call fails
    $ip = gethostbyname(gethostname());
}

// 2) CPU Usage (real approach, naive parse from top)
$cpu_usage = 0;
$topOutput = shell_exec("top -bn1 | grep 'Cpu(s)'");
if ($topOutput && preg_match('/(\d+(\.\d+)?)%us/', $topOutput, $matches)) {
    // e.g. "Cpu(s): 12.3%us, ..."
    $cpu_usage = (int) round($matches[1]);
}

// 3) RAM Usage (parse free -m)
$ram_usage = 0;
$freeOutput = shell_exec("free -m");
if ($freeOutput) {
    $lines = explode("\n", trim($freeOutput));
    if (count($lines) >= 2) {
        // e.g. "Mem: 2000 1500 ..."
        $memLine = preg_split('/\s+/', $lines[1]);
        $totalMem = (int) $memLine[1];
        $usedMem  = (int) $memLine[2];
        if ($totalMem > 0) {
            $ram_usage = (int) round(($usedMem / $totalMem) * 100);
        }
    }
}

// 4) HDD Usage (parse df for root partition)
$hdd_usage = 0;
$dfOutput = shell_exec("df -h --output=pcent / | tail -1");
if ($dfOutput) {
    // e.g. "  38%"
    $dfOutput = trim($dfOutput, "% \n\r\t");
    $hdd_usage = (int) $dfOutput; // e.g. 38
}

// 5) Uptime
$uptimeOutput = shell_exec("uptime -p");
$uptime = $uptimeOutput ? trim(str_replace("up ", "", $uptimeOutput)) : "N/A";

// 6) Additional placeholders (or real data if you have them):
// For a real system, you might parse netstat or your streaming processes.
$users           = 2;   // or parse your real user logic
$live_connections= 5;   // or netstat / streaming logic
$down_channels   = 0;   // depends on how you track "down" channels
$streams_live    = 0;   // likewise
$streams_off     = 0;   // likewise

// 7) Input/Output bandwidth usage: 
// For real usage, parse your network interface counters or a tool like ifstat.
// We'll simulate for demonstration:
$input_bw  = rand(0, 100);
$output_bw = rand(0, 100);

// 8) Update the 'servers' table for the main server (is_main=1)
$stmt = $mysqli->prepare("
  UPDATE servers 
  SET 
    cpu_usage       = ?,
    ram_usage       = ?,
    hdd_usage       = ?,
    bandwidth_usage = 0,         -- optional leftover column if you have it
    users           = ?,
    live_connections= ?,
    down_channels   = ?,
    streams_live    = ?,
    streams_off     = ?,
    input_bw        = ?,
    output_bw       = ?,
    uptime          = ?,
    ip             = ?
  WHERE is_main = 1
");
if ($stmt) {
    $stmt->bind_param("iiiiniiiiiss",
        $cpu_usage, 
        $ram_usage, 
        $hdd_usage,
        $users,
        $live_connections,
        $down_channels,
        $streams_live,
        $streams_off,
        $input_bw,
        $output_bw,
        $uptime,
        $ip
    );
    $stmt->execute();
    $stmt->close();
} else {
    die("Statement prepare error: " . $mysqli->error);
}

$mysqli->close();
echo "Server stats updated successfully.\n";
