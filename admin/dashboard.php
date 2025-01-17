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

        }
        main {
            padding: 20px;
            text-align: center;
        }
        .container {
            margin-top: 50px;
        }
        .container h1 {
            font-size: 2.5rem;
            color: #00ffff;
        }
        .container p {
            font-size: 1.2rem;
            color: #cccccc;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="/public/images/logo.png" alt="VidiQ Logo">
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
    <div class="container">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Manage your panel using the menu above.</p>
    </div>
</main>
</body>
</html>