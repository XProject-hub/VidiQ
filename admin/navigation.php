<?php
// navigation.php
// Make sure session_start() is called in your admin pages before including this file.
?>
<header class="header">
  <!-- Left section: Logo + Navigation -->
  <div class="left-section">
    <div class="logo">
      <a href="/admin/dashboard.php">
        <img src="/admin/assets/images/logo.png" alt="VidiQ Logo">
      </a>
    </div>
    <nav class="nav-links">
      <ul>
        <!-- Dashboard Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <ul class="dropdown-menu">
            <li><a href="/admin/dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/live-connections.php"><i class="fas fa-bolt"></i> Live Connections</a></li>
            <li><a href="/admin/activity-log.php"><i class="fas fa-list"></i> Activity Log</a></li>
            <li><a href="/admin/process-monitor.php"><i class="fas fa-tasks"></i> Process Monitor</a></li>
          </ul>
        </li>
        <!-- Servers Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-server"></i> Servers</a>
          <ul class="dropdown-menu">
            <li><a href="/admin/add-load-balancer.php"><i class="fas fa-plus-circle"></i> Add Load Balancer</a></li>
            <li><a href="/admin/install-load-balancer.php"><i class="fas fa-download"></i> Install Load Balancer</a></li>
            <li><a href="/admin/manage-servers.php"><i class="fas fa-cogs"></i> Manage Servers</a></li>
          </ul>
        </li>
        <!-- Management Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-tools"></i> Management</a>
          <ul class="dropdown-menu">
            <!-- Service Setup Submenu -->
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-cog"></i> Service Setup</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/packages.php"><i class="fas fa-box-open"></i> Packages</a></li>
                <li><a href="/admin/categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="/admin/groups.php"><i class="fas fa-layer-group"></i> Groups</a></li>
                <li><a href="/admin/epg.php"><i class="fas fa-tv"></i> EPG</a></li>
                <li><a href="/admin/channel-order.php"><i class="fas fa-sort"></i> Channel Order</a></li>
                <li><a href="/admin/folder-watch.php"><i class="fas fa-folder"></i> Folder Watch</a></li>
                <li><a href="/admin/subresellers.php"><i class="fas fa-user-friends"></i> SubResellers</a></li>
                <li><a href="/admin/transcode-profiles.php"><i class="fas fa-video"></i> Transcode Profiles</a></li>
                <li><a href="/admin/provider-check.php"><i class="fas fa-check-circle"></i> Provider Check</a></li>
              </ul>
            </li>
            <!-- Security Submenu -->
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-shield-alt"></i> Security</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/login-flood.php"><i class="fas fa-exclamation-triangle"></i> Login Flood</a></li>
                <li><a href="/admin/security-centar.php"><i class="fas fa-lock"></i> Security Centar</a></li>
                <li><a href="/admin/blocked-ips.php"><i class="fas fa-ban"></i> Blocked IP's</a></li>
                <li><a href="/admin/blocked-isps.php"><i class="fas fa-ban"></i> Blocked ISP's</a></li>
                <li><a href="/admin/rtmp-ips.php"><i class="fas fa-network-wired"></i> RTMP IP's</a></li>
                <li><a href="/admin/blocked-user-agents.php"><i class="fas fa-user-secret"></i> Blocked User Agents</a></li>
              </ul>
            </li>
            <!-- Tools Submenu -->
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-wrench"></i> Tools</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/mass-delete.php"><i class="fas fa-trash"></i> Mass Delete</a></li>
                <li><a href="/admin/finger-print.php"><i class="fas fa-fingerprint"></i> Finger Print</a></li>
                <li><a href="/admin/stream-tools.php"><i class="fas fa-play"></i> Stream Tools</a></li>
                <li><a href="/admin/ip-change.php"><i class="fas fa-exchange-alt"></i> IP Change</a></li>
                <li><a href="/admin/dns-covers-change.php"><i class="fas fa-globe"></i> DNS Covers Change</a></li>
                <li><a href="/admin/quick-tools.php"><i class="fas fa-bolt"></i> Quick Tools</a></li>
              </ul>
            </li>
            <!-- Logs Submenu -->
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-file-alt"></i> Logs</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/reseller-logs.php"><i class="fas fa-file-invoice-dollar"></i> Reseller Logs</a></li>
                <li><a href="/admin/credit-logs.php"><i class="fas fa-credit-card"></i> Credit Logs</a></li>
                <li><a href="/admin/login-logs.php"><i class="fas fa-sign-in-alt"></i> Login Logs</a></li>
                <li><a href="/admin/client-logs.php"><i class="fas fa-users"></i> Client Logs</a></li>
                <li><a href="/admin/stream-logs.php"><i class="fas fa-play-circle"></i> Stream Logs</a></li>
                <li><a href="/admin/line-ip-usage.php"><i class="fas fa-map-marker-alt"></i> Line IP Usage</a></li>
                <li><a href="/admin/panel-logs.php"><i class="fas fa-clipboard-list"></i> Panel Logs</a></li>
                <li><a href="/admin/mag-event-logs.php"><i class="fas fa-tv"></i> MAG Event Logs</a></li>
                <li><a href="/admin/mag-claims.php"><i class="fas fa-bullhorn"></i> MAG Claims</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <!-- Resellers Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-user-tie"></i> Resellers</a>
          <ul class="dropdown-menu">
            <li><a href="/admin/add-registered-user.php"><i class="fas fa-user-plus"></i> Add Registered User</a></li>
            <li><a href="/admin/manage-registered-users.php"><i class="fas fa-user-cog"></i> Manage Registered Users</a></li>
            <li><a href="/admin/reseller-statistics.php"><i class="fas fa-chart-line"></i> Resellers Statistics</a></li>
          </ul>
        </li>
        <!-- Users Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-users"></i> Users</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-user-friends"></i> User Lines</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-user.php"><i class="fas fa-user-plus"></i> Add User</a></li>
                <li><a href="/admin/manage-users.php"><i class="fas fa-user-cog"></i> Manage Users</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-tv"></i> MAG Devices</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-mag-device.php"><i class="fas fa-plus-square"></i> Add MAG Device</a></li>
                <li><a href="/admin/manage-mag-devices.php"><i class="fas fa-cogs"></i> Manage MAG Devices</a></li>
                <li><a href="/admin/link-mag-user.php"><i class="fas fa-link"></i> Link MAG User</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-tv"></i> Enigma Devices</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-enigma-device.php"><i class="fas fa-plus-square"></i> Add Enigma Device</a></li>
                <li><a href="/admin/manage-enigma-devices.php"><i class="fas fa-cogs"></i> Manage Enigma Devices</a></li>
                <li><a href="/admin/link-enigma-user.php"><i class="fas fa-link"></i> Link Enigma User</a></li>
              </ul>
            </li>
            <li><a href="/admin/mass-edit-users.php"><i class="fas fa-edit"></i> Mass Edit Users</a></li>
          </ul>
        </li>
        <!-- Content Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-video"></i> Content</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-stream"></i> Streams</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-stream.php">Add Stream</a></li>
                <li><a href="/admin/manage-streams.php">Manage Streams</a></li>
                <li><a href="/admin/mass-edit-streams.php">Mass Edit Streams</a></li>
                <li><a href="/admin/import-streams.php">Import Streams</a></li>
                <li><a href="/admin/streams-statistic.php">Streams Statistic</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-plus-square"></i> Create Channel</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/create-channel.php">Create Channel</a></li>
                <li><a href="/admin/manage-channels.php">Manage Channels</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <!-- VOD Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-film"></i> VOD</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-video"></i> Movies</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-movie.php">Add Movie</a></li>
                <li><a href="/admin/manage-movies.php">Manage Movies</a></li>
                <li><a href="/admin/mass-edit-movies.php">Mass Edit Movies</a></li>
                <li><a href="/admin/import-movies.php">Import Movies</a></li>
                <li><a href="/admin/movies-statistic.php">Movies Statistic</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a href="#"><i class="fas fa-tv"></i> Series</a>
              <ul class="dropdown-menu">
                <li><a href="/admin/add-serie.php">Add Serie</a></li>
                <li><a href="/admin/manage-series.php">Manage Series</a></li>
                <li><a href="/admin/manage-episodes.php">Manage Episodes</a></li>
                <li><a href="/admin/mass-edit-series.php">Mass Edit Series</a></li>
                <li><a href="/admin/mass-edit-episodes.php">Mass Edit Episodes</a></li>
                <li><a href="/admin/import-series.php">Import Series</a></li>
                <li><a href="/admin/series-statistic.php">Series Statistic</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <!-- Bouquets Dropdown -->
        <li class="dropdown">
          <a href="#"><i class="fas fa-leaf"></i> Bouquets</a>
          <ul class="dropdown-menu">
            <li><a href="/admin/add-bouquet.php">Add Bouquet</a></li>
            <li><a href="/admin/manage-bouquets.php">Manage Bouquets</a></li>
            <li><a href="/admin/order-bouquets.php">Order Bouquets</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
  <!-- Right side: user info -->
  <div class="user-info">
    <div class="user-name">
      <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?> <i class="fas fa-user-circle"></i>
    </div>
    <ul class="dropdown">
      <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
</header>

<!-- JavaScript for Click-based Dropdown Toggle -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  var toggles = document.querySelectorAll('.nav-links li.dropdown > a, .dropdown-submenu > a');
  toggles.forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      var parent = this.parentElement;
      // Toggle active class
      parent.classList.toggle('active');
      // Close siblings
      Array.from(parent.parentElement.children).forEach(function(sibling) {
        if (sibling !== parent) {
          sibling.classList.remove('active');
        }
      });
    });
  });
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    toggles.forEach(function(toggle) {
      if (!toggle.parentElement.contains(e.target)) {
        toggle.parentElement.classList.remove('active');
      }
    });
  });
});
</script>
