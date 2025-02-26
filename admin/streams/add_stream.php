<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit;
}
$username = $_SESSION['username'] ?? 'admin';

// (Optional: Process form submission if POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form fields
    $streamName   = trim($_POST['streamName'] ?? '');
    $streamURL    = trim($_POST['streamURL'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $bouquets     = trim($_POST['bouquets'] ?? '');
    $streamLogo   = trim($_POST['streamLogo'] ?? '');
    $notes        = trim($_POST['notes'] ?? '');
    
    // Advanced tab fields (example)
    $generate_pts       = isset($_POST['generate_pts']) ? 1 : 0;
    $native_frames      = isset($_POST['native_frames']) ? 1 : 0;
    $stream_all_codecs  = isset($_POST['stream_all_codecs']) ? 1 : 0;
    $allow_recording    = isset($_POST['allow_recording']) ? 1 : 0;
    $allow_rtmp         = isset($_POST['allow_rtmp']) ? 1 : 0;
    $direct_source      = isset($_POST['direct_source']) ? 1 : 0;
    $custom_channel_sid = trim($_POST['custom_channel_sid'] ?? '');
    $minute_delay       = trim($_POST['minute_delay'] ?? '');
    $ffmpeg_command     = trim($_POST['ffmpeg_command'] ?? '');
    $ondemand_probesize  = trim($_POST['ondemand_probesize'] ?? '128000');
    $user_agent         = trim($_POST['user_agent'] ?? 'Vidiq 1.0');
    $http_proxy         = trim($_POST['http_proxy'] ?? '');
    $cookie             = trim($_POST['cookie'] ?? '');
    $headers            = trim($_POST['headers'] ?? '');
    $transcoding_profile= trim($_POST['transcoding_profile'] ?? '');
    
    // Map tab
    $custom_map         = trim($_POST['custom_map'] ?? '');
    
    // Restart tab
    $days_to_restart    = trim($_POST['days_to_restart'] ?? '');
    $time_to_restart    = trim($_POST['time_to_restart'] ?? '');
    
    // EPG tab
    $epg_source         = trim($_POST['epg_source'] ?? '');
    $epg_channel_id     = trim($_POST['epg_channel_id'] ?? '');
    $epg_language       = trim($_POST['epg_language'] ?? '');
    
    // Servers tab
    $server_tree        = isset($_POST['server_tree']) ? implode(',', $_POST['server_tree']) : '';
    $on_demand          = isset($_POST['on_demand']) ? 1 : 0;
    $timeshift_days     = trim($_POST['timeshift_days'] ?? '');
    $start_stream_now   = isset($_POST['start_stream_now']) ? 1 : 0;
    
    // Insert into streams table (example, adjust columns as needed)
    require_once __DIR__ . '/../../config/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        die("DB connection error: " . $mysqli->connect_error);
    }
    $stmt = $mysqli->prepare("INSERT INTO streams (name, stream_url, category, stream_info) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        // For simplicity, we store bouquet, logo, notes into stream_info as JSON (or adjust as needed)
        $info = json_encode([
            'bouquets' => $bouquets,
            'streamLogo' => $streamLogo,
            'notes' => $notes,
            // You can add more advanced fields here
            'advanced' => [
                'generate_pts' => $generate_pts,
                'native_frames' => $native_frames,
                'stream_all_codecs' => $stream_all_codecs,
                'allow_recording' => $allow_recording,
                'allow_rtmp' => $allow_rtmp,
                'direct_source' => $direct_source,
                'custom_channel_sid' => $custom_channel_sid,
                'minute_delay' => $minute_delay,
                'ffmpeg_command' => $ffmpeg_command,
                'ondemand_probesize' => $ondemand_probesize,
                'user_agent' => $user_agent,
                'http_proxy' => $http_proxy,
                'cookie' => $cookie,
                'headers' => $headers,
                'transcoding_profile' => $transcoding_profile,
            ],
            'map' => $custom_map,
            'restart' => [
                'days_to_restart' => $days_to_restart,
                'time_to_restart' => $time_to_restart,
            ],
            'epg' => [
                'epg_source' => $epg_source,
                'epg_channel_id' => $epg_channel_id,
                'epg_language' => $epg_language,
            ],
            'servers' => [
                'server_tree' => $server_tree,
                'on_demand' => $on_demand,
                'timeshift_days' => $timeshift_days,
                'start_stream_now' => $start_stream_now,
            ]
        ]);
        $stmt->bind_param("ssss", $streamName, $streamURL, $category, $info);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
    header("Location: /admin/streams/manage_streams.php?msg=Stream+added+successfully");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Stream - VidiQ Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- External CSS for overall admin styling -->
  <link rel="stylesheet" href="/admin/assets/css/style.css">
  <style>
    html, body {
      margin: 0; 
      padding: 0;
      width: 100%;
      background: #2e2e2e;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
    }
    .wizard-container {
      background: rgba(255,255,255,0.1);
      box-shadow: 0 8px 32px rgba(31,38,135,0.37);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.18);
      margin: 50px auto;
      max-width: 900px;
      padding: 30px;
    }
    .wizard-container h2 {
      margin-top: 0;
      margin-bottom: 20px;
      font-size: 1.8rem;
      color: #00bcd4;
      text-align: center;
    }
    .tab-headers {
      display: flex;
      justify-content: space-around;
      margin-bottom: 20px;
      border-bottom: 2px solid rgba(0,0,0,0.4);
    }
    .tab-headers div {
      cursor: pointer;
      padding: 10px 15px;
      color: #00bcd4;
      font-weight: 600;
      transition: background 0.3s;
    }
    .tab-headers div:hover,
    .tab-headers div.active {
      background: rgba(0,188,212,0.3);
      border-radius: 4px;
    }
    .tab-section {
      display: none;
    }
    .tab-section.active {
      display: block;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #333;
      border-radius: 4px;
      background: rgba(31,31,31,0.8);
      color: #f0f0f0;
      box-sizing: border-box;
      font-size: 1rem;
    }
    .form-group textarea {
      resize: vertical;
    }
    .form-row {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    .form-row > .form-group {
      flex: 1;
    }
    .tab-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    .tab-buttons button {
      padding: 12px 20px;
      background-color: #00bcd4;
      border: none;
      color: #fff;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }
    .tab-buttons button:hover {
      background-color: #0097a7;
    }
    /* Toggle Switch Styles */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #f44336;
      transition: 0.4s;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: #fff;
      transition: 0.4s;
    }
    input:checked + .slider {
      background-color: #4caf50;
    }
    input:focus + .slider {
      box-shadow: 0 0 1px #4caf50;
    }
    input:checked + .slider:before {
      transform: translateX(26px);
    }
    .slider.round {
      border-radius: 34px;
    }
    .slider.round:before {
      border-radius: 50%;
    }
    /* Top actions */
    .top-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-bottom: 20px;
    }
    .top-actions a {
      padding: 8px 12px;
      background-color: #ff4081;
      color: #fff;
      border-radius: 4px;
      transition: background 0.3s;
      text-decoration: none;
    }
    .top-actions a:hover {
      background-color: #e73370;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="left-section">
      <div class="logo">
        <img src="/admin/assets/images/logo.png" alt="VidiQ Logo">
      </div>
      <?php include __DIR__ . '/../navigation.php'; ?>
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
    <div class="wizard-container">
      <div class="top-actions">
        <a href="/admin/streams/manage_streams.php">View Streams</a>
      </div>
      <h2>Add New Stream</h2>
      <!-- Tab Headers -->
      <div class="tab-headers">
        <div id="header-details" class="active" onclick="showTab('tab-details')">Details</div>
        <div id="header-advanced" onclick="showTab('tab-advanced')">Advanced</div>
        <div id="header-map" onclick="showTab('tab-map')">Map</div>
        <div id="header-restart" onclick="showTab('tab-restart')">Restart</div>
        <div id="header-epg" onclick="showTab('tab-epg')">EPG</div>
        <div id="header-servers" onclick="showTab('tab-servers')">Servers</div>
      </div>
      <!-- Form -->
      <form id="addStreamForm" method="post" action="">
        <!-- Tab: Details -->
        <div class="tab-section active" id="tab-details">
          <div class="form-group">
            <label for="streamName">Stream Name *</label>
            <input type="text" id="streamName" name="streamName" required placeholder="Stream Name">
          </div>
          <div class="form-group">
            <label for="streamURL">Stream URL *</label>
            <input type="text" id="streamURL" name="streamURL" required placeholder="http://example.com/stream.m3u8">
          </div>
          <div class="form-group">
            <label for="category">Category Type</label>
            <select id="category" name="category">
              <option value="">Select Category</option>
              <option value="Sports">Sports</option>
              <option value="Movies">Movies</option>
              <option value="News">News</option>
            </select>
          </div>
          <div class="form-group">
            <label for="bouquets">Add To Bouquets</label>
            <input type="text" id="bouquets" name="bouquets" placeholder="Bouquet Name">
          </div>
          <div class="form-group">
            <label for="streamLogo">Stream Logo URL</label>
            <input type="text" id="streamLogo" name="streamLogo" placeholder="http://example.com/logo.png">
          </div>
          <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Optional notes"></textarea>
          </div>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-advanced')">Next</button>
          </div>
        </div>
        <!-- Tab: Advanced -->
        <div class="tab-section" id="tab-advanced">
          <div class="form-row">
            <div class="form-group">
              <label>Generate PTS</label>
              <label class="switch">
                <input type="checkbox" name="generate_pts">
                <span class="slider round"></span>
              </label>
            </div>
            <div class="form-group">
              <label>Native Frames</label>
              <label class="switch">
                <input type="checkbox" name="native_frames">
                <span class="slider round"></span>
              </label>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Stream All Codecs</label>
              <label class="switch">
                <input type="checkbox" name="stream_all_codecs">
                <span class="slider round"></span>
              </label>
            </div>
            <div class="form-group">
              <label>Allow Recording</label>
              <label class="switch">
                <input type="checkbox" name="allow_recording">
                <span class="slider round"></span>
              </label>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Allow RTMP Output</label>
              <label class="switch">
                <input type="checkbox" name="allow_rtmp">
                <span class="slider round"></span>
              </label>
            </div>
            <div class="form-group">
              <label>Direct Source</label>
              <label class="switch">
                <input type="checkbox" name="direct_source">
                <span class="slider round"></span>
              </label>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Custom Channel SID</label>
              <input type="text" name="custom_channel_sid" placeholder="e.g., 1234" style="max-width: 150px;">
            </div>
            <div class="form-group">
              <label>Minute Delay</label>
              <input type="text" name="minute_delay" placeholder="e.g., 5" style="max-width: 150px;">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Custom FFmpeg Command</label>
              <input type="text" name="ffmpeg_command" placeholder="e.g., -vf scale=1280:720" style="max-width: 200px;">
            </div>
            <div class="form-group">
              <label>On Demand Probesize</label>
              <input type="text" name="ondemand_probesize" value="128000" style="max-width: 200px;">
            </div>
          </div>
          <div class="form-group">
            <label>User Agent</label>
            <input type="text" name="user_agent" value="Vidiq 1.0">
          </div>
          <div class="form-group">
            <label>HTTP Proxy</label>
            <input type="text" name="http_proxy" placeholder="http://proxy.example.com:8080">
          </div>
          <div class="form-group">
            <label>Cookie</label>
            <input type="text" name="cookie" placeholder="Cookie data">
          </div>
          <div class="form-group">
            <label>Headers</label>
            <textarea name="headers" rows="3" placeholder="Custom HTTP headers"></textarea>
          </div>
          <div class="form-group">
            <label>Transcoding Profile</label>
            <select name="transcoding_profile">
              <option value="disabled">Transcoding Disabled</option>
              <option value="low">Low Quality</option>
              <option value="high">High Quality</option>
            </select>
          </div>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-details')">Previous</button>
            <button type="button" onclick="showTab('tab-map')">Next</button>
          </div>
        </div>
        <!-- Tab: Map -->
        <div class="tab-section" id="tab-map">
          <p>Custom maps are advanced features. Only modify if you understand the settings.</p>
          <div class="form-group">
            <label>Custom Map</label>
            <input type="text" name="custom_map" placeholder="Search map...">
          </div>
          <p>No map data available</p>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-advanced')">Previous</button>
            <button type="button" onclick="showTab('tab-restart')">Next</button>
          </div>
        </div>
        <!-- Tab: Restart -->
        <div class="tab-section" id="tab-restart">
          <div class="form-group">
            <label>Days to Restart</label>
            <input type="number" name="days_to_restart" placeholder="0">
          </div>
          <div class="form-group">
            <label>Time to Restart</label>
            <input type="time" name="time_to_restart" value="00:00">
          </div>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-map')">Previous</button>
            <button type="button" onclick="showTab('tab-epg')">Next</button>
          </div>
        </div>
        <!-- Tab: EPG -->
        <div class="tab-section" id="tab-epg">
          <div class="form-group">
            <label>EPG Source</label>
            <select name="epg_source">
              <option value="">No EPG</option>
              <option value="epg1">EPG 1</option>
              <option value="epg2">EPG 2</option>
            </select>
          </div>
          <div class="form-group">
            <label>EPG Channel ID</label>
            <input type="text" name="epg_channel_id">
          </div>
          <div class="form-group">
            <label>EPG Language</label>
            <input type="text" name="epg_language">
          </div>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-restart')">Previous</button>
            <button type="button" onclick="showTab('tab-servers')">Next</button>
          </div>
        </div>
        <!-- Tab: Servers -->
        <div class="tab-section" id="tab-servers">
          <div class="form-group">
            <label for="server_tree">Server Tree</label>
            <select id="server_tree" name="server_tree[]" multiple style="height:120px;">
              <!-- Populate server options here -->
            </select>
          </div>
          <div class="form-group">
            <label for="on_demand">On Demand</label>
            <label class="switch">
              <input type="checkbox" name="on_demand" id="on_demand">
              <span class="slider round"></span>
            </label>
          </div>
          <div class="form-group">
            <label for="timeshift_days">Timeshift Days</label>
            <input type="number" name="timeshift_days" id="timeshift_days" placeholder="0">
          </div>
          <div class="form-group">
            <label>Start Stream Now?</label>
            <label class="switch">
              <input type="checkbox" name="start_stream_now" value="1">
              <span class="slider round"></span>
            </label>
          </div>
          <div class="tab-buttons">
            <button type="button" onclick="showTab('tab-epg')">Previous</button>
            <button type="submit">Add Stream</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- FOOTER -->
  <?php
  if (file_exists(__DIR__ . '/../footer.php')) {
      include __DIR__ . '/../footer.php';
  } else {
      echo '<footer class="footer"><p>&copy; ' . date("Y") . ' VidiQ. All rights reserved.</p></footer>';
  }
  ?>

  <!-- JavaScript for Tab Navigation -->
  <script>
    function showTab(tabId) {
      document.querySelectorAll('.tab-section').forEach(function(tab) {
        tab.classList.remove('active');
      });
      document.getElementById(tabId).classList.add('active');
      document.querySelectorAll('.tab-headers div').forEach(function(header) {
        header.classList.remove('active');
      });
      var headerId = 'header-' + tabId.split('-')[1];
      if(document.getElementById(headerId)){
        document.getElementById(headerId).classList.add('active');
      }
    }
  </script>
</body>
</html>
