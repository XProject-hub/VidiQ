#!/bin/bash

# Colors for output
green="\033[0;32m"
cyan="\033[0;36m"
white="\033[1;37m"
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
sudo apt install -y nginx mysql-server php-fpm php-mysql python3 python3-pip git unzip curl sqlite3

# Install required PHP and Python libraries
echo -e "${green}Installing required PHP and Python libraries...${reset}"
sudo apt install -y php-curl php-xml php-mbstring
pip3 install flask mysql-connector-python

# Configure MySQL
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 12)
echo -e "${green}Configuring MySQL...${reset}"
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASSWORD}'; FLUSH PRIVILEGES;"
sudo mysql -e "CREATE DATABASE vidiq;"

# Set up web directory
BASE_DIR="/home/Vidiq/panel"
sudo mkdir -p $BASE_DIR
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 /home/Vidiq

# Clone project from GitHub
echo -e "${green}Cloning Vidiq project from GitHub...${reset}"
git clone https://github.com/XProject-hub/vidiq.git $BASE_DIR

# Create auto.db and initialize users table
echo -e "${green}Setting up SQLite database...${reset}"
DB_PATH="$BASE_DIR/config/auto.db"
sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, password TEXT);"
sqlite3 $DB_PATH "INSERT INTO users (username, password) VALUES ('admin', '$(openssl rand -base64 12)');"

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

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf|eot)$ {
        expires max;
        log_not_found off;
    }
}"
echo "$NGINX_CONFIG" | sudo tee /etc/nginx/sites-available/vidiq
sudo ln -s /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/
sudo systemctl restart nginx

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
