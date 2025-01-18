<?php
session_start();
require_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

// Fetch the user's role from the database
$db = new PDO('sqlite:../config/vidiq_master.db'); // Adjust the path and database connection as needed
$stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VidiQ</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="/js/dashboard.js"></script>
    <style>
  
    </style>
  <script>
        // Fetch server details including CPU, RAM, bandwidth, users, streams, etc.
        function fetchServerDetails() {
            fetch('/api/get-server-stats.php')
                .then(response => response.json())
                .then(data => {
                    const serversContainer = document.getElementById('servers');
                    serversContainer.innerHTML = ''; // Clear the current servers
                    data.servers.forEach(server => {
                        serversContainer.innerHTML += `
                            <div class="server-card">
                                <h4>${server.name} - ${server.ip}</h4>
                                <div class="stats">
                                    <span><strong>Connections:</strong> ${server.connections}</span>
                                    <span><strong>Users:</strong> ${server.users}</span>
                                    <span><strong>Live Streams:</strong> ${server.streamsLive}</span>
                                    <span><strong>Offline Streams:</strong> ${server.streamsOff}</span>
                                    <span><strong>Input:</strong> ${server.input} Mbps</span>
                                    <span><strong>Output:</strong> ${server.output} Mbps</span>
                                    <span><strong>CPU Usage:</strong></span>
                                    <div class="progress-bar">
                                        <span style="width: ${server.cpu}%"></span>
                                    </div>
                                    <span><strong>RAM Usage:</strong></span>
                                    <div class="progress-bar">
                                        <span style="width: ${server.ram}%"></span>
                                    </div>
                                    <span><strong>Bandwidth:</strong> ${server.bandwidth} Mbps</span>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(error => console.error('Error fetching server stats:', error));
        }

        // Fetch stats periodically
        setInterval(fetchServerDetails, 5000); // Update every 5 seconds
        window.onload = fetchServerDetails;
    </script>
</head>
<body>
<header>
    <div class="logo">
        <img src="/images/logo.png" alt="VidiQ Logo">
    </div>
    <nav class="navbar">
        <ul>
            <li class="dropdown">
                <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Live Connections</a></li>
                    <li><a href="#">Process Monitor</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-server"></i> Servers</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Add Load Balancer</a></li>
                    <li><a href="#">Install Load Balancer</a></li>
                    <li><a href="#">Manage Servers</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-tools"></i> Management</a>
                <ul class="dropdown-menu">
                    <li class="dropdown-submenu">
                        <a href="#">Service Setup</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Packages</a></li>
                            <li><a href="#">Categories</a></li>
                            <li><a href="#">Groups</a></li>
                            <li><a href="#">EPG</a></li>
                            <li><a href="#">Channel Order</a></li>
                            <li><a href="#">Folder Watch</a></li>
                            <li><a href="#">SubResellers</a></li>
                            <li><a href="#">Provider Con Check</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Security</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Login Flood</a></li>
                            <li><a href="#">Security Center</a></li>
                            <li><a href="#">Blocked IPs</a></li>
                            <li><a href="#">Blocked ISP</a></li>
                            <li><a href="#">RTMP IP</a></li>
                            <li><a href="#">Blocked User Agents</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Tools</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Mass Delete</a></li>
                            <li><a href="#">Fingerprint</a></li>
                            <li><a href="#">Stream Tools</a></li>
                            <li><a href="#">IP Change</a></li>
                            <li><a href="#">DNS Cover Change</a></li>
                            <li><a href="#">Quick Tools</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Logs</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Reseller Logs</a></li>
                            <li><a href="#">Credit Logs</a></li>
                            <li><a href="#">Login Logs</a></li>
                            <li><a href="#">Client Logs</a></li>
                            <li><a href="#">Stream Logs</a></li>
                            <li><a href="#">Line IP Usage</a></li>
                            <li><a href="#">Panel Logs</a></li>
                            <li><a href="#">Mag Logs</a></li>
                            <li><a href="#">Mag Claims</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-users"></i> Resellers</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Add Reseller User</a></li>
                    <li><a href="#">Manage Reseller Users</a></li>
                    <li><a href="#">Resellers Statistics</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-user"></i> Users</a>
                <ul class="dropdown-menu">
                    <li class="dropdown-submenu">
                        <a href="#">User Lines</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add User</a></li>
                            <li><a href="#">Manage Users</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">MAG Devices</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add MAG Device</a></li>
                            <li><a href="#">Manage MAG Devices</a></li>
                            <li><a href="#">Link MAG User</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Enigma Devices</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add Enigma Device</a></li>
                            <li><a href="#">Manage Enigma Devices</a></li>
                            <li><a href="#">Link Enigma User</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Mass Edit Users</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-film"></i> Content</a>
                <ul class="dropdown-menu">
                    <li class="dropdown-submenu">
                        <a href="#">Streams</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add Stream</a></li>
                            <li><a href="#">Manage Streams</a></li>
                            <li><a href="#">Mass Edit Streams</a></li>
                            <li><a href="#">Import Streams</a></li>
                            <li><a href="#">Streams Statistics</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Created Channels</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Create Channel</a></li>
                            <li><a href="#">Manage Channels</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Movies</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add Movie</a></li>
                            <li><a href="#">Manage Movies</a></li>
                            <li><a href="#">Mass Edit Movies</a></li>
                            <li><a href="#">Import Movies</a></li>
                            <li><a href="#">Import Movies M3U</a></li>
                            <li><a href="#">Duplicate Movies</a></li>
                            <li><a href="#">Movies Statistics</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Series</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add Series</a></li>
                            <li><a href="#">Manage Series</a></li>
                            <li><a href="#">Manage Episodes</a></li>
                            <li><a href="#">Mass Edit Series</a></li>
                            <li><a href="#">Mass Edit Episodes</a></li>
                            <li><a href="#">Import Episodes M3U</a></li>
                            <li><a href="#">Series Statistics</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a href="#">Stations</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Add Station</a></li>
                            <li><a href="#">Manage Stations</a></li>
                            <li><a href="#">Mass Edit Stations</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-th"></i> Bouquets</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Add Bouquets</a></li>
                    <li><a href="#">Manage Bouquets</a></li>
                    <li><a href="#">Order Bouquets</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fas fa-cogs"></i> Settings</a>
                <ul class="dropdown-menu">
                    <li><a href="#">General Settings</a></li>
                    <li><a href="#">Tickets</a></li>
                    <li><a href="#">Watch Settings</a></li>
                    <li><a href="#">Backups</a></li>
                    <li><a href="#">Database</a></li>
                </ul>
            </li>
        </ul>
    </nav>
<div class="user-profile">
    <img src="/images/user-icon.png" alt="User Icon">
    <span><?php echo htmlspecialchars($_SESSION['user']); ?></span>
    <ul class="dropdown-menu">
        <li><a href="#"><i class="fas fa-user-cog"></i> Profile</a></li>
        <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
</header>
<main>
    <section class="dashboard-stats">
        <div class="stat-card online-users">
            <i class="fas fa-user"></i>
            <h3 id="online-users">0</h3>
            <p>Online Users</p>
        </div>
        <div class="stat-card open-connections">
            <i class="fas fa-network-wired"></i>
            <h3 id="open-connections">0</h3>
            <p>Open Connections</p>
        </div>
        <div class="stat-card total-input">
            <i class="fas fa-download"></i>
            <h3 id="total-input">0 Mbps</h3>
            <p>Total Input</p>
        </div>
        <div class="stat-card total-output">
            <i class="fas fa-upload"></i>
            <h3 id="total-output">0 Mbps</h3>
            <p>Total Output</p>
        </div>
        <div class="stat-card online-streams">
            <i class="fas fa-play"></i>
            <h3 id="online-streams">0</h3>
            <p>Online Streams</p>
        </div>
        <div class="stat-card offline-streams">
            <i class="fas fa-stop"></i>
            <h3 id="offline-streams">0</h3>
            <p>Offline Streams</p>
        </div>
    </section>
<section class="servers">
<div id="main-server-card" class="server-card">
    <h4>Main Server</h4>
    <div class="server-details">
        <p>Connections: <span id="server-connections">0</span></p>
        <p>Live Streams: <span id="server-live-streams">0</span></p>
    </div>
    <!-- Circular Progress Bars -->
    <div class="progress-circle">
        <svg height="100" width="100">
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="10" fill="none"></circle>
            <circle
                id="cpu-usage"
                cx="50"
                cy="50"
                r="45"
                stroke="#00ffff"
                stroke-width="10"
                fill="none"
                style="stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 0.35s;"
            ></circle>
        </svg>
        <div class="circle-label">CPU: <span id="cpu-usage-value">0%</span></div>
    </div>

    <div class="progress-circle">
        <svg height="100" width="100">
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="10" fill="none"></circle>
            <circle
                id="ram-usage"
                cx="50"
                cy="50"
                r="45"
                stroke="#00ffff"
                stroke-width="10"
                fill="none"
                style="stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 0.35s;"
            ></circle>
        </svg>
        <div class="circle-label">RAM: <span id="ram-usage-value">0%</span></div>
    </div>

    <div class="progress-circle">
        <svg height="100" width="100">
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="10" fill="none"></circle>
            <circle
                id="bandwidth-input"
                cx="50"
                cy="50"
                r="45"
                stroke="#00ffff"
                stroke-width="10"
                fill="none"
                style="stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 0.35s;"
            ></circle>
        </svg>
        <div class="circle-label">Input: <span id="bandwidth-input-value">0%</span></div>
    </div>

    <div class="progress-circle">
        <svg height="100" width="100">
            <circle cx="50" cy="50" r="45" stroke="#444" stroke-width="10" fill="none"></circle>
            <circle
                id="bandwidth-output"
                cx="50"
                cy="50"
                r="45"
                stroke="#00ffff"
                stroke-width="10"
                fill="none"
                style="stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 0.35s;"
            ></circle>
        </svg>
        <div class="circle-label">Output: <span id="bandwidth-output-value">0%</span></div>
    </div>
</div>


</section>
</main>
</body>
</html>