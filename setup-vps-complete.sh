#!/bin/bash

# ============================================
# SEMPAT APP - COMPLETE VPS SETUP SCRIPT
# ============================================
# Setup lengkap VPS dari awal sampai website live dengan SSL
# Domain: sempat-app.com
# Server: 145.79.12.217

set -e  # Exit on error

echo "=========================================="
echo "🚀 SEMPAT APP - VPS SETUP"
echo "=========================================="
echo ""

# ============================================
# 1. UPDATE SYSTEM
# ============================================
echo "📦 Updating system packages..."
apt update
apt upgrade -y

# ============================================
# 2. INSTALL PHP 8.2 & EXTENSIONS
# ============================================
echo ""
echo "🐘 Installing PHP 8.2..."
apt install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update

apt install -y php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring \
    php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl \
    php8.2-redis php8.2-soap php8.2-imagick

# Konfigurasi PHP untuk production
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 50M/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini

# ============================================
# 3. INSTALL MYSQL 8.0
# ============================================
echo ""
echo "🗄️  Installing MySQL 8.0..."
apt install -y mysql-server

# Start MySQL
systemctl start mysql
systemctl enable mysql

# Secure MySQL & Create Database
mysql <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'SempatLMS2026!';
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
CREATE DATABASE IF NOT EXISTS sempat_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'sempat_user'@'localhost' IDENTIFIED BY 'SempatLMS2026!';
GRANT ALL PRIVILEGES ON sempat_lms.* TO 'sempat_user'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "✅ Database 'sempat_lms' created"
echo "✅ User 'sempat_user' created with password 'SempatLMS2026!'"

# ============================================
# 4. INSTALL NGINX
# ============================================
echo ""
echo "🌐 Installing Nginx..."
apt install -y nginx

# Backup default config
mv /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup || true

# Start Nginx
systemctl start nginx
systemctl enable nginx

# ============================================
# 5. INSTALL COMPOSER
# ============================================
echo ""
echo "🎼 Installing Composer..."
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm /tmp/composer-setup.php

# ============================================
# 6. INSTALL NODE.JS & NPM
# ============================================
echo ""
echo "📗 Installing Node.js 20.x..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# ============================================
# 7. INSTALL REDIS
# ============================================
echo ""
echo "🔴 Installing Redis..."
apt install -y redis-server
systemctl start redis-server
systemctl enable redis-server

# ============================================
# 8. INSTALL SUPERVISOR (untuk Queue Workers)
# ============================================
echo ""
echo "👷 Installing Supervisor..."
apt install -y supervisor
systemctl start supervisor
systemctl enable supervisor

# ============================================
# 9. INSTALL CERTBOT (untuk SSL)
# ============================================
echo ""
echo "🔐 Installing Certbot..."
apt install -y certbot python3-certbot-nginx

# ============================================
# 10. INSTALL FAIL2BAN (Security)
# ============================================
echo ""
echo "🛡️  Installing Fail2ban..."
apt install -y fail2ban
systemctl start fail2ban
systemctl enable fail2ban

# ============================================
# 11. SETUP PROJECT DIRECTORY
# ============================================
echo ""
echo "📁 Creating project directory..."
mkdir -p /var/www/sempat-app
chown -R www-data:www-data /var/www/sempat-app
chmod -R 755 /var/www/sempat-app

# ============================================
# 12. CONFIGURE NGINX FOR DOMAIN (TEMPORARY - HTTP ONLY)
# ============================================
echo ""
echo "⚙️  Configuring Nginx for sempat-app.com..."
cat > /etc/nginx/sites-available/sempat-app << 'NGINX_CONFIG'
server {
    listen 80;
    listen [::]:80;
    server_name sempat-app.com www.sempat-app.com;
    
    root /var/www/sempat-app/public;
    index index.php index.html;

    # Logging
    access_log /var/log/nginx/sempat-access.log;
    error_log /var/log/nginx/sempat-error.log;

    # Laravel public folder
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_read_timeout 300;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
NGINX_CONFIG

# Enable site
ln -sf /etc/nginx/sites-available/sempat-app /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test & reload Nginx
nginx -t
systemctl reload nginx

# ============================================
# 13. SETUP UFW FIREWALL
# ============================================
echo ""
echo "🔥 Configuring Firewall..."
ufw --force enable
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
ufw reload

# ============================================
# SETUP COMPLETE
# ============================================
echo ""
echo "=========================================="
echo "✅ VPS SETUP COMPLETE!"
echo "=========================================="
echo ""
echo "📋 Summary:"
echo "  ✅ PHP 8.2 + Extensions installed"
echo "  ✅ MySQL 8.0 installed"
echo "  ✅ Nginx web server configured"
echo "  ✅ Composer installed"
echo "  ✅ Node.js 20.x + NPM installed"
echo "  ✅ Redis cache server installed"
echo "  ✅ Supervisor for queue workers installed"
echo "  ✅ Certbot for SSL ready"
echo "  ✅ Fail2ban security installed"
echo "  ✅ Firewall configured (SSH, HTTP, HTTPS)"
echo ""
echo "📊 Database Info:"
echo "  Database: sempat_lms"
echo "  User: sempat_user"
echo "  Password: SempatLMS2026!"
echo "  Root Password: SempatLMS2026!"
echo ""
echo "🌐 Domain configured for: sempat-app.com"
echo ""
echo "⚡ Next Steps:"
echo "  1. Deploy aplikasi Laravel dari Windows:"
echo "     .\deploy-to-domain.ps1"
echo ""
echo "  2. Install SSL certificate (akan dilakukan otomatis oleh deploy script)"
echo ""
