#!/bin/bash
# install.sh - VidiQ IPTV Panel Installer
# This installer automatically installs all required software, sets up Nginx,
# configures MySQL, imports the schema, creates an admin user,
# and clones the VidiQ panel repository into your target directory.
# Run as root.

# Convert to Unix line endings if needed: dos2unix install.sh

# ANSI color codes for colorful output
NC='\033[0m'
CYAN='\033[36m'
GREEN='\033[32m'
YELLOW='\033[33m'
MAGENTA='\033[35m'
RED='\033[31m'

# Branded header with ASCII art
cat << "EOF"
  __     ___  ___   ___  ___  ___  ___  ___  
  \ \   / / |/ / | / / |/ _ \/ _ \/ _ \/ _ \ 
   \ \ / /| ' /| |/ /| | | | | | | | | | | | |
    \ V / | . \|   / | | |_| | |_| | |_| | |_| |
     \_/  |_|\_\_|\_\ |_|\___/ \___/ \___/ \___/ 

EOF
echo -e "${CYAN}Welcome to the VidiQ IPTV Panel Installer${NC}"
echo -e "${CYAN}=============================================${NC}"

# Ensure the script is run as root
if [ "$EUID" -ne 0 ]; then
  echo -e "${YELLOW}Please run as root (use sudo).${NC}"
  exit 1
fi

# Prompt for target installation directory (default: /home/vidiq)
read -p "$(echo -e ${CYAN}"Enter target installation directory [default: /home/vidiq]: "${NC})" TARGET_DIR
if [ -z "$TARGET_DIR" ]; then
  TARGET_DIR="/home/vidiq"
fi
echo -e "${GREEN}Target installation directory set to: $TARGET_DIR${NC}"
mkdir -p "$TARGET_DIR"

# Prompt for domain (default: localhost)
read -p "$(echo -e ${CYAN}"Enter your domain (e.g., yourdomain.com or server IP) [default: localhost]: "${NC})" DOMAIN
if [ -z "$DOMAIN" ]; then
  DOMAIN="localhost"
fi
echo -e "${GREEN}Domain set to: $DOMAIN${NC}"

# Install system dependencies
echo -e "${CYAN}Installing system dependencies...${NC}"
apt-get update
apt-get install -y nginx php-fpm php-mysql git mysql-server mysql-client

# Generate Nginx configuration file
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
NGINX_CONFIG="${TARGET_DIR}/install/nginx.conf"
mkdir -p "${TARGET_DIR}/install"
cat > "$NGINX_CONFIG" <<EOL
server {
    listen 80;
    server_name yourdomain.com;
    root /home/vidiq/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    # Alias for /admin/ requests to files outside the document root
    location /admin/ {
        alias /home/vidiq/admin/;
        index dashboard.php;
        try_files $uri $uri/ =404;

        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/run/php/php8.1-fpm.sock;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
    }
}

EOL
echo -e "${GREEN}Nginx configuration file generated at: $NGINX_CONFIG${NC}"

# Install and enable Nginx site configuration
echo -e "${CYAN}Installing and enabling Nginx site configuration...${NC}"
cp "$NGINX_CONFIG" /etc/nginx/sites-available/vidiq
ln -sf /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/vidiq
nginx -t && systemctl reload nginx

# Database setup: prompt for credentials
read -p "$(echo -e ${CYAN}"Enter your database host [default: localhost]: "${NC})" DB_HOST
if [ -z "$DB_HOST" ]; then
  DB_HOST="localhost"
fi
read -p "$(echo -e ${CYAN}"Enter the name for your panel database: "${NC})" DB_NAME
read -p "$(echo -e ${CYAN}"Enter the desired panel database username: "${NC})" DB_USER
read -p "$(echo -e ${CYAN}"Enter the desired panel database password: "${NC})" DB_PASS
read -p "$(echo -e ${CYAN}"Enter MySQL root password (for creating database and user): "${NC})" MYSQL_ROOT_PASS

# Write config file
CONFIG_DIR="${TARGET_DIR}/config"
mkdir -p "$CONFIG_DIR"
cat > "$CONFIG_DIR/config.php" <<EOL
<?php
define('DB_HOST', '${DB_HOST}');
define('DB_USER', '${DB_USER}');
define('DB_PASS', '${DB_PASS}');
define('DB_NAME', '${DB_NAME}');
define('SITE_URL', 'http://${DOMAIN}');
?>
EOL
echo -e "${GREEN}Configuration file written to ${CONFIG_DIR}/config.php${NC}"

# Create database and user
echo -e "${CYAN}Creating database and user...${NC}"
mysql -uroot -p"${MYSQL_ROOT_PASS}" -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
mysql -uroot -p"${MYSQL_ROOT_PASS}" -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -uroot -p"${MYSQL_ROOT_PASS}" -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"

# Import schema from schema.sql in the installer folder
SCHEMA_PATH="$(dirname "$0")/schema.sql"
if [ ! -f "$SCHEMA_PATH" ]; then
  echo -e "${YELLOW}Error: schema.sql file not found at $SCHEMA_PATH${NC}"
else
  mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < "$SCHEMA_PATH"
  echo -e "${GREEN}Database schema imported successfully.${NC}"
fi

# Create panel admin login
read -p "$(echo -e ${CYAN}"Enter the desired admin username: "${NC})" PANEL_ADMIN_USER
read -p "$(echo -e ${CYAN}"Enter the desired admin password: "${NC})" PANEL_ADMIN_PASS
mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "CREATE TABLE IF NOT EXISTS admin (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -e "INSERT INTO admin (username, password) VALUES ('${PANEL_ADMIN_USER}', MD5('${PANEL_ADMIN_PASS}'));"
echo -e "${GREEN}Admin user created successfully.${NC}"

# Clone repository into temporary folder then overwrite public and admin directories
echo -e "${CYAN}Cloning panel files from GitHub...${NC}"
TEMP_DIR="/tmp/vidiq_clone"
rm -rf "$TEMP_DIR"
git clone https://github.com/XProject-hub/VidiQ.git "$TEMP_DIR"
if [ -d "$TEMP_DIR/public" ] && [ -d "$TEMP_DIR/admin" ]; then
  rm -rf "${TARGET_DIR}/public" "${TARGET_DIR}/admin"
  cp -r "$TEMP_DIR/public" "$TARGET_DIR/"
  cp -r "$TEMP_DIR/admin" "$TARGET_DIR/"
  echo -e "${GREEN}Repository files updated successfully in ${TARGET_DIR}.${NC}"
  rm -rf "$TEMP_DIR"
else
  echo -e "${RED}Error: Cloned repository does not contain required directories.${NC}"
fi

SERVER_IP=$(hostname -I | awk '{print $1}')
echo -e "${GREEN}\nInstallation complete.${NC}"
echo -e "${GREEN}Access your VidiQ IPTV Panel login page at: http://$SERVER_IP${NC}"
