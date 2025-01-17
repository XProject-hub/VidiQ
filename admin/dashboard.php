<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /public/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VidiQ</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1b1b1b;
            color: #00ffff;
        }
        header {
            background-color: #2c2c2c;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo img {
            height: 40px;
        }
        .navbar {
            display: flex;
            align-items: center;
        }
        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar ul li {
            position: relative;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #00ffff;
            padding: 10px 15px;
            display: block;
        }
        .navbar ul li a:hover {
            background-color: #008b8b;
            color: #fff;
        }
        .navbar ul li .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #2c2c2c;
            list-style: none;
            margin: 0;
            padding: 0;
            min-width: 200px;
            z-index: 1000;
        }
        .navbar ul li .dropdown-menu li {
            position: relative;
        }
        .navbar ul li .dropdown-menu li a {
            padding: 10px 15px;
        }
        .navbar ul li:hover > .dropdown-menu {
            display: block;
        }
        .navbar ul li .dropdown-menu .dropdown-submenu {
            position: relative;
        }
        .navbar ul li .dropdown-menu .dropdown-submenu:hover > .dropdown-menu {
            display: block;
            left: 100%;
            top: 0;
        }
        .navbar ul li .dropdown-menu .dropdown-submenu .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #2c2c2c;
            top: 0;
            left: 100%;
            min-width: 200px;
        }
 .user-profile {
    position: relative;
    display: flex;
    align-items: center;
    padding: 10px 15px;
    cursor: pointer;
    color: #00ffff;
}

.user-profile img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px;
}

.user-profile span {
    font-size: 0.9rem;
    font-weight: bold;
    color: #00ffff;
}

.user-profile:hover .dropdown-menu {
    display: block;
}

.user-profile .dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 40px;
    background-color: #2c2c2c;
    list-style: none;
    margin: 0;
    padding: 0;
    min-width: 200px;
    z-index: 1000;
    border: 1px solid #008b8b;
    border-radius: 5px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.user-profile .dropdown-menu li {
    border-bottom: 1px solid #333;
}

.user-profile .dropdown-menu li:last-child {
    border-bottom: none;
}

.user-profile .dropdown-menu li a {
    display: flex;
    align-items: center;
    text-decoration: none;
    padding: 10px 15px;
    color: #00ffff;
    font-size: 0.9rem;
}

.user-profile .dropdown-menu li a:hover {
    background-color: #008b8b;
    color: #ffffff;
}

.user-profile .dropdown-menu li a i {
    margin-right: 10px;
}

          body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1b1b1b;
            color: #fff;
        }
        header {
            background-color: #2c2c2c;
            padding: 10px;
            text-align: center;
        }
        header img {
            height: 50px;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            text-align: center;
            padding: 15px;
            position: relative;
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
        }
        .stat-card i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #fff;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
            color: #fff;
        }
        .servers {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }
        .server-card {
            background-color: #2c2c2c;
            border-radius: 10px;
            padding: 15px;
            color: #00ffff;
            animation: slideIn 1s ease-in-out;
        }
        .server-card h4 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: #fff;
        }
        .server-stats {
            margin-bottom: 10px;
        }
        .server-stats span {
            display: inline-block;
            width: 50%;
            font-size: 0.85rem;
        }
        .progress-bar {
            height: 6px;
            background-color: #444;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 5px;
        }
        .progress-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #00ffff, #00bcd4);
            transition: width 0.3s ease-in-out;
        }
        /* Card colors */
        .stat-card.online-users {
            background-color: #4caf50; /* Green */
        }
        .stat-card.open-connections {
            background-color: #2196f3; /* Blue */
        }
        .stat-card.total-input {
            background-color: #ff5722; /* Orange */
        }
        .stat-card.total-output {
            background-color: #e91e63; /* Pink */
        }
        .stat-card.online-streams {
            background-color: #9c27b0; /* Purple */
        }
        .stat-card.offline-streams {
            background-color: #ff9800; /* Amber */
        }
              }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes slideIn {
            from {
                transform: translateY(10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

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
    <section class="servers" id="servers">
        <!-- Dynamically populated server cards -->
    </section>
</main>
<script>
    function fetchStats() {
        fetch('/api/get-dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('online-users').innerText = data.onlineUsers;
                document.getElementById('open-connections').innerText = data.openConnections;
                document.getElementById('total-input').innerText = `${data.totalInput} Mbps`;
                document.getElementById('total-output').innerText = `${data.totalOutput} Mbps`;
                document.getElementById('online-streams').innerText = data.onlineStreams;
                document.getElementById('offline-streams').innerText = data.offlineStreams;
            });

        fetch('/api/get-server-stats.php')
            .then(response => response.json())
            .then(data => {
                const serversContainer = document.getElementById('servers');
                serversContainer.innerHTML = '';
                data.servers.forEach(server => {
                    serversContainer.innerHTML += `
                        <div class="server-card">
                            <h4>${server.name} - ${server.ip}</h4>
                            <div class="server-stats">
                                <span>Connections: ${server.connections}</span>
                                <span>Users: ${server.users}</span>
                            </div>
                            <div class="server-stats">
                                <span>Live Streams: ${server.streamsLive}</span>
                                <span>Offline Streams: ${server.streamsOff}</span>
                            </div>
                            <div class="server-stats">
                                <span>Input: ${server.input}</span>
                                <span>Output: ${server.output}</span>
                            </div>
                            <div class="server-stats">
                                <span>CPU Usage:</span>
                                <div class="progress-bar"><span style="width: ${server.cpu}%"></span></div>
                            </div>
                            <div class="server-stats">
                                <span>RAM Usage:</span>
                                <div class="progress-bar"><span style="width: ${server.ram}%"></span></div>
                            </div>
                            <div class="server-stats">
                                <span>Bandwidth:</span>
                                <div class="progress-bar"><span style="width: ${server.bandwidth}%"></span></div>
                            </div>
                        </div>
                    `;
                });
            });
    }

    setInterval(fetchStats, 5000);
    window.onload = fetchStats;
</script>
</body>
</html>