#!/bin/bash

# Display Welcome Message
echo "========================================="
echo "   Welcome to VidiQ"
echo "   Developed by X Project"
echo "========================================="
echo "This script will install the VidiQ IPTV Panel."
echo "Please ensure you are running this as root and on Ubuntu 20 or 22."
echo ""

# Ensure the script is run as root
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

# Gather user input
read -p "Enter the domain name for the VidiQ panel: " domain_name
read -p "Enter the GitHub repository link [https://github.com/XProject-hub/VidiQ]: " repo_link
repo_link=${repo_link:-https://github.com/XProject-hub/VidiQ}

# Update and upgrade the system
apt-get update && apt-get upgrade -y

# Install Nginx, PHP, Python, and necessary extensions
apt-get install nginx python3 python3-pip php php-fpm php-mysql -y

# Clone the VidiQ repository
cd /home
mkdir vidiq
cd vidiq
git clone $repo_link panel

# Set permissions
chown -R www-data:www-data /home/vidiq/panel
chmod -R 755 /home/vidiq/panel

# Configure Nginx
cat <<EOF > /etc/nginx/sites-available/vidiq
server {
    listen 80;
    server_name $domain_name;

    root /home/vidiq/panel/public;
    index index.php index.html index.htm;

    location / {
        try_files \$uri \$uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
ln -s /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/

# Restart Nginx to apply the new configuration
systemctl restart nginx
systemctl enable nginx

# Install and configure the database
apt-get install mariadb-server -y
systemctl start mariadb
systemctl enable mariadb

# Secure the database installation
mysql_secure_installation

# Run the automated SQL setup
mysql -u root -p < /home/vidiq/panel/scripts/db_automated.sql

# Generate admin login credentials
admin_username="admin"
admin_password=`openssl rand -base64 12`

# Display completion message
echo "======================"
echo "Thank you for installing VidiQ"
echo "======================"
echo "Login Details:"
echo "Username: $admin_username"
echo "Password: $admin_password"
echo "Panel Link: http://$domain_name/"
echo "======================"
