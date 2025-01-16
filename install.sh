#!/bin/bash

# Display Welcome Message
echo "========================================="
echo "   Welcome to VidiQ"
echo "   Developed by X Project"
echo "========================================="
echo "This script will install the VidiQ IPTV Panel."
echo ""

# Ensure the script is run as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root"
  exit
fi

# Gather user input
read -p "Enter the domain name for the VidiQ panel: " domain_name
repo_link="https://github.com/XProject-hub/VidiQ"

# Update and install dependencies
apt-get update && apt-get upgrade -y
apt-get install nginx python3 python3-pip php php-fpm php-mysql mariadb-server -y

# Clone the repository
mkdir -p /home/vidiq
cd /home/vidiq
git clone $repo_link panel

# Set permissions
chown -R www-data:www-data /home/vidiq/panel
chmod -R 755 /home/vidiq/panel

# Configure Nginx
cat <<EOF > /etc/nginx/sites-available/vidiq
server {
    listen 80;
    server_name localhost;

    root /home/vidiq/panel/public;
    index login.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
ln -s /etc/nginx/sites-available/vidiq /etc/nginx/sites-enabled/
systemctl restart nginx

# Configure MariaDB and run SQL script
systemctl start mariadb
systemctl enable mariadb

echo "Enter the MariaDB root password:"
mysql -u root -p < /home/vidiq/panel/scripts/db_automated.sql

# Create default admin credentials
admin_username="admin"
admin_password=`openssl rand -base64 12`
encrypted_password=`php -r "echo password_hash('$admin_password', PASSWORD_DEFAULT);"`

mysql -u root -p -e "
USE vidiq_db;
INSERT INTO admins (username, password, email) VALUES ('$admin_username', '$encrypted_password', 'admin@vidiq.com');
"

# Display completion message
echo "======================"
echo "Thank you for installing VidiQ"
echo "======================"
echo "Login Details:"
echo "Username: $admin_username"
echo "Password: $admin_password"
echo "Panel Link: http://$domain_name/"
echo "======================"
