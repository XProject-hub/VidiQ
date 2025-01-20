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
BASE_DIR="/home/VidiQ/"
sudo mkdir -p $BASE_DIR/config
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 /home/VidiQ

# Clone project from GitHub
echo -e "${green}Cloning VidiQ project from GitHub...${reset}"
if [ -d "$BASE_DIR" ]; then
    echo -e "${green}Directory already exists, pulling latest changes...${reset}"
    git -C $BASE_DIR reset --hard
    git -C $BASE_DIR clean -fd
    git -C $BASE_DIR pull
else
    git clone https://github.com/XProject-hub/vidiq.git $BASE_DIR
fi
chmod +x $BASE_DIR/install.sh

# Save Main Server Information
echo -e "${green}Saving main server details...${reset}"
MAIN_SERVER_IP=$(hostname -I | awk '{print $1}')
MAIN_SERVER_NAME=$(hostname)
CONFIG_DIR="$BASE_DIR/config"

mkdir -p $CONFIG_DIR
echo "{\"ip\": \"$MAIN_SERVER_IP\", \"name\": \"$MAIN_SERVER_NAME\"}" > $CONFIG_DIR/main_server.json
echo -e "${green}Main server details saved at $CONFIG_DIR/main_server.json${reset}"

# Create auto.db and initialize users table
echo -e "${green}Setting up SQLite database...${reset}"
DB_PATH="$BASE_DIR/config/auto.db"
if [ ! -f "$DB_PATH" ]; then
    # Create directory and set permissions if they haven't been set yet
    sudo mkdir -p $(dirname "$DB_PATH")
    sudo chmod -R 755 $(dirname "$DB_PATH")
    
    # Initialize SQLite database and create users table
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'Viewer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );"
    
    # Insert admin user with a random password and an email
    ADMIN_PASSWORD=$(openssl rand -base64 12)
    ADMIN_EMAIL="admin@example.com"  # Default admin email
    sqlite3 $DB_PATH "INSERT INTO users (username, email, password, role) 
    VALUES ('admin', '$ADMIN_EMAIL', '$ADMIN_PASSWORD', 'Admin');"
    echo -e "${green}Admin user created with random password: ${ADMIN_PASSWORD}${reset}"


    # Create server_details table
    sqlite3 $DB_PATH "CREATE TABLE IF NOT EXISTS server_details (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        server_name TEXT NOT NULL,
        connections INT NOT NULL,
        live_streams INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );"
    
    # Insert default data for Main Server
    sqlite3 $DB_PATH "INSERT INTO server_details (server_name, connections, live_streams)
    VALUES ('Main Server', 100, 80);"
    
    echo -e "${green}Database initialized with server details.${reset}"
else
    echo -e "${green}SQLite database already exists. Skipping creation.${reset}"
fi


# Configure Nginx
echo -e "${green}Configuring Nginx...${reset}"
NGINX_CONFIG="server {
    listen 80;
    server_name _;

    root /home/VidiQ/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location /admin/ {
        alias /home/VidiQ/admin/;
        index dashboard.php;
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.1-fpm.sock;
            fastcgi_param SCRIPT_FILENAME \$request_filename;
            include fastcgi_params;
        }
    }

    location ~ \.php$ {
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
