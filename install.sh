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
echo -e "${white}Welcome to Vidiq${reset}"
echo -e "${white}Developed by X Project${reset}"
echo -e "${cyan}================${reset}"

# Update system and install dependencies
echo -e "${green}Updating system and installing dependencies...${reset}"
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php-fpm php-mysql git unzip curl sqlite3

# Configure MySQL
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
echo -e "${green}Configuring MySQL...${reset}"

# Unmask and re-enable MySQL
sudo systemctl unmask mysql.service
sudo systemctl enable mysql.service

# Remove FROZEN file if it exists
if [ -f /etc/mysql/FROZEN ]; then
    echo -e "${yellow}Removing /etc/mysql/FROZEN to allow reconfiguration.${reset}"
    sudo rm /etc/mysql/FROZEN
fi

# Purge MySQL if it's in a broken state
echo -e "${green}Ensuring clean MySQL installation...${reset}"
sudo apt remove --purge -y mysql-server mysql-client mysql-common
sudo apt autoremove -y
sudo rm -rf /etc/mysql /var/lib/mysql

# Reinstall MySQL
echo -e "${green}Reinstalling MySQL...${reset}"
sudo apt install -y mysql-server

# Start MySQL service
if ! sudo systemctl start mysql; then
    echo -e "${red}MySQL service failed to start. Check logs.${reset}"
    exit 1
fi

# Configure MySQL if service is running
if sudo systemctl is-active --quiet mysql; then
    sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASSWORD}'; FLUSH PRIVILEGES;"
    sudo mysql -e "CREATE DATABASE vidiq;"
else
    echo -e "${red}MySQL service is not running. Skipping MySQL configuration.${reset}"
    exit 1
fi

# Set up web directory
BASE_DIR="/home/Vidiq/panel"
sudo mkdir -p $BASE_DIR/config
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 /home/Vidiq

# Clone project from GitHub
echo -e "${green}Cloning Vidiq project from GitHub...${reset}"
if [ -d "$BASE_DIR" ]; then
    echo -e "${green}Directory already exists, pulling latest changes...${reset}"
    git -C $BASE_DIR reset --hard
    git -C $BASE_DIR clean -fd
    git -C $BASE_DIR pull
else
    git clone https://github.com/XProject-hub/vidiq.git $BASE_DIR
fi
chmod +x $BASE_DIR/install.sh

# Create auto.db and initialize tables
echo -e "${green}Setting up SQLite database...${reset}"
DB_PATH="$BASE_DIR/config/auto.db"
if [ ! -f "$DB_PATH" ]; then
    sudo mkdir -p $(dirname "$DB_PATH")
    sudo chmod -R 755 $(dirname "$DB_PATH")
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, password TEXT);"
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS streams (id INTEGER PRIMARY KEY, name TEXT, url TEXT);"
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS resellers (id INTEGER PRIMARY KEY, username TEXT, email TEXT, password TEXT);"
    sqlite3 $DB_PATH "INSERT INTO users (username, password) VALUES ('admin', '$(openssl rand -base64 12)');"
else
    echo -e "${green}SQLite database already exists. Skipping creation.${reset}"
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS streams (id INTEGER PRIMARY KEY, name TEXT, url TEXT);"
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS resellers (id INTEGER PRIMARY KEY, username TEXT, email TEXT, password TEXT);"
fi

# Configure Nginx
echo -e "${green}Configuring Nginx...${reset}"
NGINX_CONFIG="server {
    listen 80;
    server_name _;

    root $BASE_DIR/public;
    index index.php index.html index.htm;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf|eot)$ {
        expires max;
        log_not_found off;
    }
}"
NGINX_SITE_PATH="/etc/nginx/sites-available/vidiq"
echo "$NGINX_CONFIG" | sudo tee $NGINX_SITE_PATH

if [ -L /etc/nginx/sites-enabled/vidiq ]; then
    echo -e "${green}Existing Nginx configuration detected, updating symbolic link...${reset}"
    sudo rm /etc/nginx/sites-enabled/vidiq
fi
sudo ln -s $NGINX_SITE_PATH /etc/nginx/sites-enabled/
if ! sudo nginx -t; then
    echo -e "${red}Nginx configuration test failed. Skipping restart.${reset}"
else
    sudo systemctl restart nginx || echo -e "${red}Failed to restart Nginx. Check configuration.${reset}"
fi

# Generate default user
USERNAME="admin"
PASSWORD=$(openssl rand -base64 12)
echo -e "${green}Generating default user...${reset}"
sqlite3 $DB_PATH "INSERT INTO users (username, password) VALUES ('$USERNAME', '$PASSWORD');"

# Final message
IP=$(hostname -I | awk '{print $1}')
echo -e "${cyan}================${reset}"
echo -e "${white}Thank you for installing VidiQ${reset}"
echo -e "${cyan}================${reset}"
echo -e "${white}Your MySQL Password: ${MYSQL_ROOT_PASSWORD}${reset}"
echo -e "${white}SQLite Database Path: ${DB_PATH}${reset}"
echo -e "${white}Username: ${USERNAME}${reset}"
echo -e "${white}Password: ${PASSWORD}${reset}"
echo -e "${white}Panel URL: http://${IP}${reset}"