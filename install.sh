#!/bin/bash

# Ensure the script is run as root
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

# Update and upgrade the system
apt-get update && apt-get upgrade -y

# Install necessary packages
apt-get install -y apache2 mysql-server php libapache2-mod-php php-mysql git curl

# Set up MySQL database
echo "Setting up VidiQ database..."
mysql -e "CREATE DATABASE vidiq_db;"
mysql -e "CREATE USER 'vidiq_user'@'localhost' IDENTIFIED BY 'your_password';"
mysql -e "GRANT ALL PRIVILEGES ON vidiq_db.* TO 'vidiq_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Clone the VidiQ source from GitHub
cd /home
git clone https://github.com/your-username/VidiQ-Panel.git VidiQ

# Set directory permissions
chown -R www-data:www-data /home/VidiQ
chmod -R 755 /home/VidiQ

# Restart Apache to apply changes
systemctl restart apache2

echo "Installation is complete. VidiQ Panel is now set up and ready to use."
