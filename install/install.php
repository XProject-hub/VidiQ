<?php
/**
 * install.php
 *
 * A comprehensive installer for the VidiQ IPTV Panel.
 * This version:
 * - Installs required system packages: Nginx, PHP-FPM, PHP-MySQL, Git, MySQL Server and Client.
 * - Automatically detects the installed PHP version and generates an Nginx configuration file listening on port 80.
 * - Automatically copies the generated configuration to /etc/nginx/sites-available/vidiq, creates a symlink in /etc/nginx/sites-enabled, tests, and reloads Nginx.
 * - Prompts for database credentials and creates the database and user automatically.
 * - Writes /config/config.php with your chosen database credentials.
 * - Sets up the MySQL database schema (from schema.sql).
 * - Automatically creates an "admin" table if it doesn't exist.
 * - Prompts for panel admin login details and inserts an admin record.
 * - Clones the panel files from GitHub.
 * - Displays the server IP so you can access the panel login page at http://<server-ip>, which will serve /home/vidiq/public/index.php.
 *
 * Run this script as root in CLI mode.
 */

// --- Helper Functions ---

/**
 * Automatically detect the PHP version (major.minor) from the command line.
 *
 * @return string
 */
function get_php_version() {
    $version = trim(shell_exec("php -r 'echo PHP_MAJOR_VERSION.\".\".PHP_MINOR_VERSION;'"));
    return $version ? $version : "7.4"; // fallback to 7.4 if detection fails
}

/**
 * Generate the Nginx configuration file content based on provided parameters.
 * Uses the detected PHP version for the PHP-FPM socket and listens on port 80.
 *
 * @param string $domain
 * @return string
 */
function generate_nginx_config($domain) {
    $phpVersion = get_php_version();
    $phpSocket = "unix:/run/php/php{$phpVersion}-fpm.sock";
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

/**
 * Executes a shell command and outputs its result.
 *
 * @param string $cmd
 */
function run_command($cmd) {
    echo "Running: $cmd\n";
    exec($cmd, $output, $retval);
    foreach ($output as $line) {
        echo $line . "\n";
    }
    if ($retval !== 0) {
        echo "Command exited with error code: $retval\n";
    }
    echo "\n";
}

/**
 * Check if the current script is run as root.
 *
 * @return bool
 */
function is_root() {
    return (function_exists('posix_geteuid') && posix_geteuid() === 0);
}

/**
 * Get the server IP address.
 *
 * @return string
 */
function get_server_ip() {
    // Try using hostname -I to get the network IP(s)
    $ip = trim(shell_exec("hostname -I"));
    if ($ip) {
        $ips = explode(" ", $ip);
        foreach ($ips as $candidate) {
            if ($candidate !== '127.0.0.1' && !empty($candidate)) {
                return $candidate;
            }
        }
    }
    return gethostbyname(gethostname());
}

/**
 * Write the configuration file /config/config.php using provided database details.
 *
 * @param string $dbHost
 * @param string $dbUser
 * @param string $dbPass
 * @param string $dbName
 * @param string $domain
 */
function write_config_file($dbHost, $dbUser, $dbPass, $dbName, $domain) {
    $configContent = <<<PHP
<?php
// /config/config.php

define('DB_HOST', '{$dbHost}');
define('DB_USER', '{$dbUser}');
define('DB_PASS', '{$dbPass}');
define('DB_NAME', '{$dbName}');

// Other global configurations
define('SITE_URL', 'http://{$domain}');
?>
PHP;
    $configPath = __DIR__ . '/../config/config.php';
    if (file_put_contents($configPath, $configContent) !== false) {
        echo "Configuration file written to {$configPath}\n";
    } else {
        echo "Error: Unable to write configuration file to {$configPath}\n";
    }
}

// --- Installation Steps for CLI Mode ---
function run_cli_mode() {
    echo "VidiQ Comprehensive Installer (CLI Mode)\n";
    echo "=========================================\n\n";

    if (!is_root()) {
        echo "WARNING: Please run this script as root (or with sudo).\n";
        exit(1);
    }
    
    // Step 1: Install system dependencies (Nginx, PHP-FPM, PHP-MySQL, Git, MySQL)
    echo "Step 1: Installing system dependencies...\n";
    run_command("apt-get update");
    run_command("apt-get install -y nginx php-fpm php-mysql git mysql-server mysql-client");

    // Step 2: Generate Nginx configuration file
    echo "Step 2: Generating Nginx configuration file...\n";
    echo "Enter your domain (e.g., yourdomain.com): ";
    $domain = trim(fgets(STDIN));
    $nginxConfig = generate_nginx_config($domain);
    $configFile = "nginx.conf";
    if (file_put_contents($configFile, $nginxConfig) !== false) {
        echo "Nginx configuration file generated successfully as {$configFile}.\n";
    } else {
        echo "Error: Unable to write the Nginx configuration file.\n";
    }
    
    // Step 2.5: Automatically install and enable Nginx site configuration
    echo "Step 2.5: Installing and enabling Nginx site configuration...\n";
    run_command("cp {$configFile} /etc/nginx/sites-available/vidiq");
    run_command("ln -sf /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/vidiq");
    run_command("nginx -t");
    run_command("systemctl reload nginx");

    // Step 3: Clone Panel Files from GitHub (Simulated)
    echo "Step 3: Cloning panel files from GitHub...\n";
    run_command("git clone https://github.com/XProject-hub/VidiQ.git /home/vidiq");

    // Step 4: Database Setup and Panel Configuration
    echo "Step 4: Database Setup and Panel Configuration\n";
    echo "Enter your database host [default: localhost]: ";
    $dbHost = trim(fgets(STDIN));
    if (empty($dbHost)) { $dbHost = 'localhost'; }
    
    echo "Enter the name for your panel database: ";
    $dbName = trim(fgets(STDIN));
    
    echo "Enter the desired panel database username: ";
    $dbUser = trim(fgets(STDIN));
    
    echo "Enter the desired panel database password: ";
    $dbPass = trim(fgets(STDIN));

    echo "Enter MySQL root password (for creating database and user): ";
    $mysqlRootPass = trim(fgets(STDIN));

    write_config_file($dbHost, $dbUser, $dbPass, $dbName, $domain);

    echo "Creating database and user...\n";
    run_command("mysql -uroot -p{$mysqlRootPass} -e \"CREATE DATABASE IF NOT EXISTS {$dbName};\"");
    run_command("mysql -uroot -p{$mysqlRootPass} -e \"CREATE USER IF NOT EXISTS '{$dbUser}'@'localhost' IDENTIFIED BY '{$dbPass}';\"");
    run_command("mysql -uroot -p{$mysqlRootPass} -e \"GRANT ALL PRIVILEGES ON {$dbName}.* TO '{$dbUser}'@'localhost'; FLUSH PRIVILEGES;\"");

    // Step 5: Setup MySQL Database Schema
    echo "Step 5: Setting up the MySQL database schema...\n";
    $schemaPath = __DIR__ . '/schema.sql';
    if (!file_exists($schemaPath)) {
        echo "Error: schema.sql file not found in the install directory.\n";
    } else {
        $schema = file_get_contents($schemaPath);
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($mysqli->connect_error) {
            echo "Connection failed: " . $mysqli->connect_error . "\n";
        } else {
            if ($mysqli->multi_query($schema)) {
                echo "Database schema installed successfully.\n";
                while ($mysqli->next_result()) {;}
            } else {
                echo "Error installing database schema: " . $mysqli->error . "\n";
            }
            $mysqli->close();
        }
    }
    
    // Step 6: Create Panel Admin Login
    echo "Step 6: Creating panel admin login...\n";
    echo "Enter the desired admin username: ";
    $adminUser = trim(fgets(STDIN));
    echo "Enter the desired admin password: ";
    $adminPass = trim(fgets(STDIN));
    
    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($mysqli->connect_error) {
        echo "Connection failed when creating admin login: " . $mysqli->connect_error . "\n";
    } else {
        $createTableQuery = "CREATE TABLE IF NOT EXISTS admin (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        if (!$mysqli->query($createTableQuery)) {
            echo "Error creating admin table: " . $mysqli->error . "\n";
        }
        $adminUserEscaped = $mysqli->real_escape_string($adminUser);
        $adminPassEscaped = $mysqli->real_escape_string($adminPass);
        $insertQuery = "INSERT INTO admin (username, password) VALUES ('{$adminUserEscaped}', MD5('{$adminPassEscaped}'))";
        if ($mysqli->query($insertQuery)) {
            echo "Admin user created successfully.\n";
        } else {
            echo "Error creating admin user: " . $mysqli->error . "\n";
        }
        $mysqli->close();
    }
    
    $serverIP = get_server_ip();
    echo "\nInstallation complete.\n";
    echo "Access your VidiQ IPTV Panel login page at: http://{$serverIP}\n";
}

// --- Main Execution ---
if (php_sapi_name() === 'cli') {
    run_cli_mode();
} else {
    echo "<p>This installer is intended for CLI mode. Please run it from the command line.</p>";
}
?>
