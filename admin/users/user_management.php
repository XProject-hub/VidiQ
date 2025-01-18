<?php
session_start();
if (!isset($_SESSION['user'], $_SESSION['role'])) {
    header('Location: /index.php');
    exit;
}

// This assumes you're storing the user's role in session as well.
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <section class="user-management">
        <h1>User Management</h1>
        <?php if ($role === 'Admin'): ?>
            <button id="add-user-btn" class="btn btn-primary">Add New User</button>
        <?php endif; ?>
        <table id="user-table" class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <!-- User data will be dynamically loaded here -->
            </tbody>
        </table>
    </section>

    <!-- User Modal -->
    <div id="user-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title">Add User</h2>
            <form id="user-form">
                <input type="hidden" name="id" id="user-id">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div>
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="Viewer">Viewer</option>
                        <option value="Editor">Editor</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Save</button>
            </form>
        </div>
    </div>
</main>
<script src="/public/js/user_management.js"></script>
</body>
</html>
