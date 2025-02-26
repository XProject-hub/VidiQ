<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'admin';

// Include DB config
require_once __DIR__ . '/../config/config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("DB connection error: " . $mysqli->connect_error);
}

// Global Stats from servers table
$globalStatsQuery = "
  SELECT
    SUM(live_connections) AS open_connections,
    SUM(users) AS online_users,
    SUM(streams_live) AS online_streams,
    SUM(streams_off) AS offline_streams,
    SUM(input_bw) AS total_input,
    SUM(output_bw) AS total_output
  FROM servers
";
$resultGlobal = $mysqli->query($globalStatsQuery);
$globalStats = $resultGlobal ? $resultGlobal->fetch_assoc() : [
    'open_connections' => 0,
    'online_users'     => 0,
    'online_streams'   => 0,
    'offline_streams'  => 0,
    'total_input'      => 0,
    'total_output'     => 0
];
if ($resultGlobal) {
    $resultGlobal->free();
}

// Servers data
$serversQuery = "
  SELECT 
    id, name, ip, cpu_usage, ram_usage, hdd_usage,
    users, live_connections, streams_live, streams_off,
    input_bw, output_bw, uptime
  FROM servers
  ORDER BY is_main DESC, id ASC
";
$resultServers = $mysqli->query($serversQuery);
$servers = [];
if ($resultServers) {
    while ($row = $resultServers->fetch_assoc()) {
        $servers[] = $row;
    }
    $resultServers->free();
}
$mysqli->close();

// Helper for circle color based on usage
function getUsageColor($percent) {
    if ($percent < 55) {
        return "#4caf50"; // green
    } elseif ($percent < 90) {
        return "#ffeb3b"; // yellow
    } else {
        return "#f44336"; // red
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - VidiQ Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Admin CSS -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">
  <style>
    html, body {
      margin: 0; padding: 0;
      width: 100%;
      background: #2e2e2e; /* Darker background */
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .dashboard-container {
      width: 100%;
      padding: 20px 0;
      box-sizing: border-box;
    }
    .dashboard-title {
      font-size: 2rem;
      text-align: center;
      color: #fff;
      margin-bottom: 20px;
      
    }
    /* STAT BOXES: 6 boxes in a single row, spanning full width */
    .stats-boxes {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 20px;
      width: 100%;
      padding: 0 20px;
      margin-bottom: 40px;
      box-sizing: border-box;
      padding-top: 75px;
    }
    .stat-box {
      background: #00bcd4; /* default color, can override below */
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      padding: 25px;
      text-align: center;
      font-weight: 600;
      color: #fff;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .stat-box img {
      width: 40px;
      height: 40px;
      margin-bottom: 10px;
    }
    .stat-title {
      font-size: 1.1rem;
      margin-bottom: 8px;
    }
    .stat-value {
      font-size: 1.6rem;
    }
    /* Specific color overrides */
    .box-cyan    { background: #00bcd4; }
    .box-green   { background: #4caf50; }
    .box-purple  { background: #9c27b0; }
    .box-red     { background: #f44336; }
    .box-orange  { background: #ff9800; }
    .box-blue    { background: #2196f3; }

    /* SERVER CARDS */
    .server-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 0 20px;
      box-sizing: border-box;
      justify-content: flex-start;
    }
    .server-card {
      background: rgba(255,255,255,0.1);
      box-shadow: 0 8px 32px rgba(31,38,135,0.37);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.18);
      color: #fff; /* White text on dark background */
      width: 360px;
      padding: 20px;
      transition: transform 0.3s ease;
      text-align: center;
    }
    .server-card:hover {
      transform: translateY(-5px);
    }
    .server-card h2 {
      font-size: 1.3rem;
      margin-bottom: 10px;
      color: #00bcd4;
    }
    .server-stats {
      font-size: 1rem;
      color: #fff;
      margin-bottom: 15px;
    }
    .server-stats p {
      margin: 6px 0;
    }
    /* Input/Output usage bars */
    .usage-bar-container {
      margin-top: 10px;
      text-align: center;
    }
    .usage-bar-label {
      font-size: 0.95rem;
      margin-bottom: 4px;
      color: #fff;
    }
    .usage-bar {
      background: #aaa;
      border-radius: 4px;
      width: 100%;
      height: 10px;
      position: relative;
    }
    .usage-fill {
      position: absolute;
      top: 0; left: 0;
      height: 100%;
      border-radius: 4px;
      transition: width 0.4s ease;
    }
    .fill-green { background: #4caf50; }
    .fill-blue  { background: #2196f3; }
    .usage-value {
      font-size: 0.9rem;
      color: #fff;
      margin-top: 4px;
      text-align: center;
    }
    /* Circles row for CPU/MEM/HDD */
    .circles-row {
      display: flex;
      gap: 20px;
      justify-content: center;
      margin-top: 20px;
    }
    .usage-circle {
      position: relative;
      width: 70px;
      height: 70px;
    }
    .usage-circle svg {
      position: absolute;
      top: 0;
      left: 0;
    }
    .circle-bg {
      stroke: #aaa;
      fill: none;
      stroke-width: 8;
    }
    .circle-fg {
      fill: none;
      stroke-width: 8;
      stroke-linecap: round;
      transition: stroke-dashoffset 0.4s ease;
    }
    .circle-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 0.75rem;
      text-align: center;
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Include your existing top navigation -->
  <?php include __DIR__ . '/navigation.php'; ?>

    
    <!-- Global Stats Boxes -->
    <div class="stats-boxes">
      <!-- 1: Open Connections -->
      <div class="stat-box box-cyan">
        <img src="/admin/assets/images/icon-connections.png" alt="Connections">
        <div class="stat-title">Open Connections</div>
        <div class="stat-value"><?php echo $globalStats['open_connections']; ?></div>
      </div>
      <!-- 2: Online Users -->
      <div class="stat-box box-green">
        <img src="/admin/assets/images/icon-users.png" alt="Users">
        <div class="stat-title">Online Users</div>
        <div class="stat-value"><?php echo $globalStats['online_users']; ?></div>
      </div>
      <!-- 3: Online Streams -->
      <div class="stat-box box-purple">
        <img src="/admin/assets/images/icon-online-streams.png" alt="Online Streams">
        <div class="stat-title">Online Streams</div>
        <div class="stat-value"><?php echo $globalStats['online_streams']; ?></div>
      </div>
      <!-- 4: Offline Streams -->
      <div class="stat-box box-red">
        <img src="/admin/assets/images/icon-offline-streams.png" alt="Offline Streams">
        <div class="stat-title">Offline Streams</div>
        <div class="stat-value"><?php echo $globalStats['offline_streams']; ?></div>
      </div>
      <!-- 5: Total Input -->
      <div class="stat-box box-orange">
        <img src="/admin/assets/images/icon-input.png" alt="Input">
        <div class="stat-title">Total Input</div>
        <div class="stat-value"><?php echo $globalStats['total_input']; ?> Mbps</div>
      </div>
      <!-- 6: Total Output -->
      <div class="stat-box box-blue">
        <img src="/admin/assets/images/icon-output.png" alt="Output">
        <div class="stat-title">Total Output</div>
        <div class="stat-value"><?php echo $globalStats['total_output']; ?> Mbps</div>
      </div>
    </div>
    
    <!-- Server Cards (main server + load balancers) -->
    <div class="server-cards">
      <?php if (!empty($servers)): ?>
        <?php
        // For circle usage calculations
        $r = 30;
        $circ = 2 * 3.1415 * $r;
        foreach ($servers as $srv):
          $cpuPercent = (int)$srv['cpu_usage'];
          $memPercent = (int)$srv['ram_usage'];
          $hddPercent = (int)$srv['hdd_usage'];
          
          $offsetCPU = $circ - ($cpuPercent / 100 * $circ);
          $offsetMEM = $circ - ($memPercent / 100 * $circ);
          $offsetHDD = $circ - ($hddPercent / 100 * $circ);
        ?>
          <div class="server-card">
            <h2><?php echo htmlspecialchars($srv['name']); ?><br><?php echo htmlspecialchars($srv['ip']); ?></h2>
            <div class="server-stats">
              <p>Conns.: <?php echo (int)$srv['live_connections']; ?></p>
              <p>Users: <?php echo (int)$srv['users']; ?></p>
              <p>Online Streams: <?php echo (int)$srv['streams_live']; ?></p>
            </div>
            <!-- Input usage -->
            <div class="usage-bar-container">
              <label class="usage-bar-label">Input</label>
              <div class="usage-bar">
                <div class="usage-fill fill-green" style="width: <?php echo (int)$srv['input_bw']; ?>%;"></div>
              </div>
              <div class="usage-value"><?php echo (int)$srv['input_bw']; ?>%</div>
            </div>
            <!-- Output usage -->
            <div class="usage-bar-container">
              <label class="usage-bar-label">Output</label>
              <div class="usage-bar">
                <div class="usage-fill fill-blue" style="width: <?php echo (int)$srv['output_bw']; ?>%;"></div>
              </div>
              <div class="usage-value"><?php echo (int)$srv['output_bw']; ?>%</div>
            </div>
            <!-- CPU/MEM/HDD usage circles -->
            <div class="circles-row">
              <?php
                // Render a circle
                function makeCircle($percent, $label) {
                    global $circ;
                    $offset = $circ - ($percent / 100 * $circ);
                    $color = getUsageColor($percent);
                    return '
                    <div class="usage-circle">
                      <svg width="70" height="70">
                        <circle class="circle-bg" cx="35" cy="35" r="30"></circle>
                        <circle class="circle-fg" cx="35" cy="35" r="30"
                                stroke-dasharray="' . $circ . '"
                                stroke-dashoffset="' . $offset . '"
                                style="stroke:' . $color . ';">
                        </circle>
                      </svg>
                      <div class="circle-text">' . $label . '<br>' . $percent . '%</div>
                    </div>
                    ';
                }
              ?>
              <?php echo makeCircle($cpuPercent, "CPU"); ?>
              <?php echo makeCircle($memPercent, "MEM"); ?>
              <?php echo makeCircle($hddPercent, "HDD"); ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; color: #fff;">No servers found. Please update your server info.</p>
      <?php endif; ?>
    </div>
  </div>
  <footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> VidiQ. All rights reserved.</p>
  </footer>
</body>
</html>
