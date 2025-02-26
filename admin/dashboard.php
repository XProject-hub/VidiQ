<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'admin';

// Connect to DB
require_once __DIR__ . '/../config/config.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("DB connection error: " . $mysqli->connect_error);
}

// Fetch all servers (main server and load balancers)
$query = "
    SELECT 
      id, name, ip, cpu_usage, ram_usage, bandwidth_usage,
      users, live_connections, down_channels, streams_live, streams_off,
      input_bw, output_bw, uptime
    FROM servers
    ORDER BY is_main DESC, id ASC
";
$result = $mysqli->query($query);
$servers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $servers[] = $row;
    }
    $result->free();
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - VidiQ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- External CSS -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">
  <!-- Inline CSS for refined glassy server cards -->
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    }
    .dashboard-container {
      margin: 40px 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: flex-start; /* align left */
    }
    .server-card {
      background: rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(31,38,135,0.37);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.18);
      color: #f0f0f0;
      width: 100%;
      max-width: 320px;
      padding: 15px;
      transition: transform 0.3s ease;
      text-align: center; /* center text in card */
    }
    .server-card:hover {
      transform: translateY(-5px);
    }
    .server-card h3 {
      font-size: 1.1rem;
      margin-bottom: 4px;
      color: #00bcd4;
    }
    .server-card p {
      font-size: 0.85rem;
      margin: 4px 0 8px;
    }
    .circle-container {
      display: flex;
      justify-content: space-around;
      margin: 10px 0;
    }
    .circle-chart {
      position: relative;
      width: 70px;
      height: 70px;
    }
    .circle-chart svg {
      position: absolute;
      top: 0;
      left: 0;
    }
    .circle-bg {
      stroke: rgba(255,255,255,0.2);
      fill: none;
      stroke-width: 8;
    }
    .circle-fg {
      fill: none;
      stroke-width: 8;
      stroke-linecap: round;
      transition: stroke-dashoffset 0.4s ease;
    }
    /* Colors for circles */
    .cpu-chart .circle-fg { stroke: #4caf50; }   /* Green */
    .ram-chart .circle-fg { stroke: #ff9800; }   /* Orange */
    .bw-chart  .circle-fg { stroke: #2196f3; }     /* Blue */
    .circle-chart .metric-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 0.75rem;
      color: #fff;
    }
    .stats-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-top: 10px;
    }
    .stat-box {
      background: rgba(31,31,31,0.7);
      padding: 6px 8px;
      border-radius: 6px;
      text-align: center;
      min-width: 60px;
      flex: 1 1 60px;
    }
    .stat-box h4 {
      font-size: 0.75rem;
      margin-bottom: 3px;
      color: #ffdd57;
    }
    .stat-box p {
      font-size: 0.75rem;
      margin: 0;
    }
    /* Progress bar for Input/Output */
    .progress-bar {
      background: rgba(255,255,255,0.2);
      border-radius: 4px;
      width: 100%;
      height: 8px;
      margin-top: 4px;
    }
    .progress-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.4s ease;
    }
    .progress-input { background: #4caf50; }
    .progress-output { background: #2196f3; }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="left-section">
      <div class="logo">
        <img src="/admin/assets/images/logo.png" alt="VidiQ Logo">
      </div>
      <?php include __DIR__ . '/navigation.php'; ?>
    </div>
    <div class="user-info">
      <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
      <ul class="dropdown">
        <li><a href="/admin/profile.php">Profile</a></li>
        <li><a href="/admin/account.php">Account</a></li>
        <li><a href="/admin/logout.php">Logout</a></li>
      </ul>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="dashboard-container">
      <?php if (!empty($servers)): ?>
        <?php foreach ($servers as $srv): ?>
          <?php
            // Use same circle dimensions for each metric
            $r = 35;
            $circ = 2 * 3.1415 * $r;
            $cpuPercent = (int)$srv['cpu_usage'];
            $ramPercent = (int)$srv['ram_usage'];
            $bwPercent  = (int)$srv['bandwidth_usage'];
            $offsetCPU = $circ - ($cpuPercent / 100 * $circ);
            $offsetRAM = $circ - ($ramPercent / 100 * $circ);
            $offsetBW  = $circ - ($bwPercent  / 100 * $circ);
          ?>
          <div class="server-card">
            <h3><?php echo htmlspecialchars($srv['name']); ?><br><?php echo htmlspecialchars($srv['ip']); ?></h3>
            <p>Uptime: <?php echo htmlspecialchars($srv['uptime']); ?></p>
            <div class="circle-container">
              <!-- CPU Circle -->
              <div class="circle-chart cpu-chart">
                <svg width="70" height="70">
                  <circle class="circle-bg" cx="35" cy="35" r="<?php echo $r; ?>"></circle>
                  <circle class="circle-fg" cx="35" cy="35" r="<?php echo $r; ?>"
                          stroke-dasharray="<?php echo $circ; ?>"
                          stroke-dashoffset="<?php echo $offsetCPU; ?>"></circle>
                </svg>
                <div class="metric-text"><?php echo $cpuPercent; ?>%</div>
              </div>
              <!-- RAM Circle -->
              <div class="circle-chart ram-chart">
                <svg width="70" height="70">
                  <circle class="circle-bg" cx="35" cy="35" r="<?php echo $r; ?>"></circle>
                  <circle class="circle-fg" cx="35" cy="35" r="<?php echo $r; ?>"
                          stroke-dasharray="<?php echo $circ; ?>"
                          stroke-dashoffset="<?php echo $offsetRAM; ?>"></circle>
                </svg>
                <div class="metric-text"><?php echo $ramPercent; ?>%</div>
              </div>
              <!-- Bandwidth Circle -->
              <div class="circle-chart bw-chart">
                <svg width="70" height="70">
                  <circle class="circle-bg" cx="35" cy="35" r="<?php echo $r; ?>"></circle>
                  <circle class="circle-fg" cx="35" cy="35" r="<?php echo $r; ?>"
                          stroke-dasharray="<?php echo $circ; ?>"
                          stroke-dashoffset="<?php echo $offsetBW; ?>"></circle>
                </svg>
                <div class="metric-text"><?php echo $bwPercent; ?>%</div>
              </div>
            </div>
            <div class="stats-row">
              <div class="stat-box">
                <h4>Conns.</h4>
                <p><?php echo (int)$srv['live_connections']; ?></p>
              </div>
              <div class="stat-box">
                <h4>Users</h4>
                <p><?php echo (int)$srv['users']; ?></p>
              </div>
              <div class="stat-box">
                <h4>Streams Live</h4>
                <p><?php echo (int)$srv['streams_live']; ?></p>
              </div>
              <div class="stat-box">
                <h4>Streams Off</h4>
                <p><?php echo (int)$srv['streams_off']; ?></p>
              </div>
              <div class="stat-box">
                <h4>Input</h4>
                <div class="progress-bar">
                  <div class="progress-fill progress-input" style="width: <?php echo (int)$srv['input_bw']; ?>%;"></div>
                </div>
                <p><?php echo (int)$srv['input_bw']; ?>%</p>
              </div>
              <div class="stat-box">
                <h4>Output</h4>
                <div class="progress-bar">
                  <div class="progress-fill progress-output" style="width: <?php echo (int)$srv['output_bw']; ?>%;"></div>
                </div>
                <p><?php echo (int)$srv['output_bw']; ?>%</p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center;">No servers found. Please update your server info.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- FOOTER -->
  <?php
  if (file_exists(__DIR__ . '/footer.php')) {
      include __DIR__ . '/footer.php';
  } else {
      echo '<footer class="footer"><p>&copy; ' . date("Y") . ' VidiQ. All rights reserved.</p></footer>';
  }
  ?>
  
  <!-- Optional auto-refresh script -->
  <script>
    // setInterval(() => { location.reload(); }, 30000);
  </script>
</body>
</html>
