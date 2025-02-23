<?php
/**
 * install.php
 *
 * A simple installer that generates an Nginx configuration file for the VidiQ IPTV Panel.
 * This script can run in both browser and CLI modes.
 */

function generate_nginx_config($domain, $phpVersion) {
    // Build the PHP-FPM socket path using the provided PHP version
    $phpSocket = "unix:/run/php/php{$phpVersion}-fpm.sock";
    
    // Create the configuration content
    $config = <<<NGINXCONF
server {
    listen 80;
    server_name {$domain};
    root /home/vidiq/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass {$phpSocket};
    }
}
NGINXCONF;
    return $config;
}

function run_browser_mode() {
    // Display a simple HTML form for user input
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "<h1>VidiQ Nginx Config Generator</h1>";
        echo "<form method='post'>";
        echo "Domain: <input type='text' name='domain' placeholder='yourdomain.com' required><br><br>";
        echo "PHP Version (e.g., 7.4): <input type='text' name='php_version' value='7.4' required><br><br>";
        echo "<input type='submit' value='Generate Config'>";
        echo "</form>";
        exit;
    }
    
    // Get POST values
    $domain = trim($_POST['domain']);
    $phpVersion = trim($_POST['php_version']);
    
    $config = generate_nginx_config($domain, $phpVersion);
    
    // Attempt to write the configuration file in the current directory
    $filename = "nginx.conf";
    if (file_put_contents($filename, $config) !== false) {
        echo "<h2>Nginx Configuration Generated Successfully</h2>";
        echo "<p>Configuration file saved as <strong>{$filename}</strong>.</p>";
        echo "<pre>" . htmlspecialchars($config) . "</pre>";
        echo "<p>Please move this file to your Nginx configuration directory (e.g., /etc/nginx/sites-available) and enable it as needed.</p>";
    } else {
        echo "<p>Error: Unable to write the configuration file.</p>";
    }
}

function run_cli_mode() {
    // Use CLI prompts to get input
    echo "VidiQ Nginx Config Generator (CLI Mode)\n";
    echo "Enter your domain (e.g., yourdomain.com): ";
    $domain = trim(fgets(STDIN));
    
    echo "Enter your PHP version (e.g., 7.4): ";
    $phpVersion = trim(fgets(STDIN));
    
    $config = generate_nginx_config($domain, $phpVersion);
    
    // Write the configuration file in the current directory
    $filename = "nginx.conf";
    if (file_put_contents($filename, $config) !== false) {
        echo "Configuration file generated successfully as {$filename}.\n";
        echo "-----------------------------\n";
        echo $config . "\n";
        echo "-----------------------------\n";
        echo "Please move this file to your Nginx configuration directory (e.g., /etc/nginx/sites-available) and enable it as needed.\n";
    } else {
        echo "Error: Unable to write the configuration file.\n";
    }
}

// Determine the mode based on the PHP SAPI
if (php_sapi_name() === 'cli') {
    run_cli_mode();
} else {
    run_browser_mode();
}
?>
