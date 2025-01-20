#!/bin/bash
set -e
trap 'echo -e "${red}An error occurred. Check the logs for details.${reset}"' ERR

# Colors for output
green="\033[0;32m"
cyan="\033[0;36m"
white="\033[1;37m"
red="\033[0;31m"
reset="\033[0m"

# Banner
clear
echo -e "${cyan}======================================${reset}"
echo -e "${white}Welcome to the Automated VidiQ Installer${reset}"
echo -e "${white}Developed by X Project${reset}"
echo -e "${cyan}======================================${reset}"

# Update system and install dependencies
echo -e "${green}Updating system and installing dependencies...${reset}"
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php-fpm php-mysql git unzip curl sqlite3 php-sqlite3

# Generate secure credentials
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
VIDIQ_DB_PASSWORD=$(openssl rand -base64 12)
VIDIQ_DB_NAME="vidiq_$(openssl rand -hex 4)"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD=$(openssl rand -base64 12)

# Stop MySQL service to ensure proper reset
echo -e "${green}Resetting MySQL root password...${reset}"
sudo systemctl stop mysql
sudo mysqld_safe --skip-grant-tables & sleep 5

# Reset MySQL root password
mysql -u root <<EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';
EOF

# Restart MySQL service
sudo killall mysqld_safe
sudo systemctl start mysql

# Configure MySQL database and user
echo -e "${green}Configuring MySQL database and user...${reset}"
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOF
CREATE DATABASE IF NOT EXISTS ${VIDIQ_DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'vidiq'@'localhost' IDENTIFIED BY '${VIDIQ_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${VIDIQ_DB_NAME}.* TO 'vidiq'@'localhost';
FLUSH PRIVILEGES;
EOF

# Set up project directory
BASE_DIR="/home/VidiQ"
echo -e "${green}Setting up project directory at ${BASE_DIR}...${reset}"
sudo mkdir -p $BASE_DIR
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 $BASE_DIR

# Clone project from GitHub
echo -e "${green}Cloning or updating VidiQ project from GitHub...${reset}"
if [ -d "$BASE_DIR/.git" ]; then
    cd $BASE_DIR
    git stash
    git pull
    git stash pop || true
else
    git clone https://github.com/XProject-hub/VidiQ.git $BASE_DIR
fi

# Create and initialize SQLite database
DB_PATH="$BASE_DIR/config/database.sqlite"
echo -e "${green}Initializing SQLite database...${reset}"
mkdir -p "$BASE_DIR/config"
sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'Viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"
ADMIN_HASHED_PASSWORD=$(php -r "echo password_hash('${ADMIN_PASSWORD}', PASSWORD_BCRYPT);")
EXISTING_ADMIN=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM users WHERE email = '$ADMIN_EMAIL';")
if [ "$EXISTING_ADMIN" -eq 0 ]; then
    sqlite3 $DB_PATH "INSERT INTO users (username, email, password, role) VALUES ('admin', '$ADMIN_EMAIL', '$ADMIN_HASHED_PASSWORD', 'Admin');"
    echo -e "${green}Admin user created.${reset}"
else
    echo -e "${cyan}Admin user already exists. Skipping creation.${reset}"
fi

# Configure PHP
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo -e "${green}Configuring PHP...${reset}"
sudo sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/${PHP_VERSION}/fpm/php.ini
sudo systemctl restart php${PHP_VERSION}-fpm

# Configure Nginx
echo -e "${green}Configuring Nginx...${reset}"
NGINX_CONFIG="server {
    listen 80;
    server_name _;

    root $BASE_DIR/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \\\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf|eot)\$ {
        expires max;
        log_not_found off;
    }
}"
echo "$NGINX_CONFIG" | sudo tee /etc/nginx/sites-available/vidiq
sudo ln -sf /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx

# Final Message
MAIN_SERVER_IP=$(hostname -I | awk '{print $1}')
echo -e "${cyan}======================================${reset}"
echo -e "${white}Installation Complete!${reset}"
echo -e "${cyan}======================================${reset}"
echo -e "${white}MySQL Root Password: ${MYSQL_ROOT_PASSWORD}${reset}"
echo -e "${white}MySQL VidiQ DB Name: ${VIDIQ_DB_NAME}${reset}"
echo -e "${white}MySQL VidiQ DB Password: ${VIDIQ_DB_PASSWORD}${reset}"
echo -e "${white}SQLite Database Path: ${DB_PATH}${reset}"
echo -e "${white}Admin Email: ${ADMIN_EMAIL}${reset}"
echo -e "${white}Admin Password: ${ADMIN_PASSWORD}${reset}"
echo -e "${white}Access your panel at: http://${MAIN_SERVER_IP}${reset}"
