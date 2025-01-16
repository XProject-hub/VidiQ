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
sudo apt install -y apache2 php libapache2-mod-php php-sqlite3 sqlite3 python3 python3-pip git unzip curl

# Set up project directory
BASE_DIR="/home/Vidiq/panel"
echo -e "${green}Setting up project directory at ${BASE_DIR}...${reset}"
sudo mkdir -p $BASE_DIR
sudo chown -R $USER:$USER $BASE_DIR
sudo chmod -R 755 /home/Vidiq

# Clone project from GitHub
echo -e "${green}Cloning Vidiq project from GitHub...${reset}"
git clone https://github.com/XProject-hub/vidiq.git $BASE_DIR

# Create SQLite database
DB_PATH="$BASE_DIR/config/auto.db"
echo -e "${green}Creating SQLite database at ${DB_PATH}...${reset}"
mkdir -p "$BASE_DIR/config"
sqlite3 $DB_PATH <<EOF
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL
);
EOF

# Insert default admin user
DEFAULT_USERNAME="admin"
DEFAULT_PASSWORD=$(openssl rand -base64 12)
HASHED_PASSWORD=$(php -r "echo password_hash('${DEFAULT_PASSWORD}', PASSWORD_BCRYPT);")
sqlite3 $DB_PATH <<EOF
INSERT INTO users (username, password) VALUES ('${DEFAULT_USERNAME}', '${HASHED_PASSWORD}');
EOF

# Configure Apache
echo -e "${green}Configuring Apache...${reset}"
APACHE_CONFIG="<VirtualHost *:80>
    ServerAdmin admin@localhost
    DocumentRoot $BASE_DIR/public
    ErrorLog $BASE_DIR/logs/error.log
    CustomLog $BASE_DIR/logs/access.log combined
</VirtualHost>"
echo "$APACHE_CONFIG" | sudo tee /etc/apache2/sites-available/vidiq.conf
sudo a2ensite vidiq.conf
sudo a2enmod rewrite
sudo systemctl restart apache2

# Final message
IP=$(hostname -I | awk '{print $1}')
echo -e "${cyan}================${reset}"
echo -e "${white}Thank you for installing VidiQ${reset}"
echo -e "${cyan}================${reset}"
echo -e "${white}Your MySQL Password: N/A (SQLite Used)${reset}"
echo -e "${white}Username: ${DEFAULT_USERNAME}${reset}"
echo -e "${white}Password: ${DEFAULT_PASSWORD}${reset}"
echo -e "${white}Panel URL: http://${IP}${reset}"
