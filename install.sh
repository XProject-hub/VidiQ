#!/bin/bash

# Colors for output
green="\033[0;32m"
cyan="\033[0;36m"
white="\033[1;37m"
red="\033[0;31m"
reset="\033[0m"

# Banner
clear
echo -e "${cyan}================${reset}"
echo -e "${white}Welcome to VidiQ${reset}"
echo -e "${white}Developed by X Project${reset}"
echo -e "${cyan}================${reset}"

# Update system and install dependencies
echo -e "${green}Updating system and installing dependencies...${reset}"
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php-fpm php-mysql git unzip curl sqlite3

# Configure MySQL
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
echo -e "${green}Configuring MySQL...${reset}"
sudo systemctl unmask mysql.service
sudo systemctl enable mysql.service
sudo systemctl start mysql
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASSWORD}'; FLUSH PRIVILEGES;"
sudo mysql -e "CREATE DATABASE vidiq;"

# Set up web directory
BASE_DIR="/home/VidiQ"
CONFIG_DIR="$BASE_DIR/config"
sudo mkdir -p $CONFIG_DIR
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 $BASE_DIR

# Clone project from GitHub
echo -e "${green}Cloning VidiQ project from GitHub...${reset}"
git clone https://github.com/XProject-hub/vidiq.git $BASE_DIR || (cd $BASE_DIR && git pull)

# Save Main Server Information
echo -e "${green}Saving main server details...${reset}"
MAIN_SERVER_IP=$(hostname -I | awk '{print $1}')
MAIN_SERVER_NAME=$(hostname)
echo "{\"ip\": \"$MAIN_SERVER_IP\", \"name\": \"$MAIN_SERVER_NAME\"}" > $CONFIG_DIR/main_server.json

# Create and initialize SQLite database
echo -e "${green}Setting up SQLite database...${reset}"
DB_PATH="$CONFIG_DIR/auto.db"
sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'Viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);"
ADMIN_PASSWORD=$(openssl rand -base64 12)
ADMIN_EMAIL="admin@example.com"
sqlite3 $DB_PATH "INSERT INTO users (username, email, password, role) VALUES ('admin', '$ADMIN_EMAIL', '$ADMIN_PASSWORD', 'Admin');"

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
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php$(php -r 'echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;')-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf|eot)$ {
        expires max;
        log_not_found off;
    }
}"
echo "$NGINX_CONFIG" | sudo tee /etc/nginx/sites-available/vidiq
sudo ln -sf /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx

# Final message
echo -e "${cyan}================${reset}"
echo -e "${white}Thank you for installing VidiQ${reset}"
echo -e "${cyan}================${reset}"
echo -e "${white}MySQL Password: ${MYSQL_ROOT_PASSWORD}${reset}"
echo -e "${white}Admin Email: ${ADMIN_EMAIL}${reset}"
echo -e "${white}Admin Password: ${ADMIN_PASSWORD}${reset}"
echo -e "${white}Panel URL: http://${MAIN_SERVER_IP}${reset}"
