# 🚀 Deployment Guide - VPS Hostinger

Panduan lengkap deploy Sempat App ke VPS Hostinger (145.79.12.217)

---

## 📋 Pre-requisites

✅ SSH key sudah di-generate (DONE)
```
C:\Users\ASUS/.ssh/id_ed25519
C:\Users\ASUS/.ssh/id_ed25519.pub
```

✅ Server IP: **145.79.12.217**  
✅ SSH User: **root**

---

## 🔧 Setup Awal (One-time Setup)

### Step 1: Upload SSH Key ke Server

```powershell
.\setup-ssh-key.ps1
```

**Akan diminta password root** (dari email Hostinger untuk pertama kali)

Setelah berhasil, test koneksi:
```powershell
ssh root@145.79.12.217
```

Seharusnya login **tanpa diminta password** ✅

### Step 2: Setup Server Environment

Jalankan script untuk install PHP, MySQL, Nginx, Composer, Node.js, dll:

```powershell
ssh root@145.79.12.217 'bash -s' < server-setup.sh
```

Atau login dulu lalu run manual:
```bash
ssh root@145.79.12.217
curl -o setup.sh https://raw.githubusercontent.com/your-repo/server-setup.sh
chmod +x setup.sh
./setup.sh
```

Script akan install:
- ✅ PHP 8.2 + extensions
- ✅ MySQL 8.0
- ✅ Nginx
- ✅ Composer
- ✅ Node.js 20.x + NPM
- ✅ Redis
- ✅ Supervisor (queue workers)
- ✅ Fail2ban (security)
- ✅ Database: `sempat_lms`
- ✅ User: `sempat_user` / Password: `SempatLMS2026!`

### Step 3: Update .env.production

Edit file `.env.production` dengan database credentials:

```env
DB_DATABASE=sempat_lms
DB_USERNAME=sempat_user
DB_PASSWORD=SempatLMS2026!
```

---

## 🚀 Deploy Aplikasi

### Deploy Complete (First time / Major changes)

```powershell
.\deploy-vps.ps1
```

Script akan otomatis:
1. ✅ Test SSH connection
2. ✅ Build assets (npm run build)
3. ✅ Backup deployment sebelumnya
4. ✅ Upload semua file ke server
5. ✅ Upload .env.production sebagai .env
6. ✅ Run composer install
7. ✅ Run migrations
8. ✅ Cache configurations
9. ✅ Set permissions
10. ✅ Restart services

### Quick Deploy (Minor changes)

Skip build & backup untuk deploy cepat:

```powershell
.\deploy-vps.ps1 -QuickDeploy
```

### Deploy Options

```powershell
# Skip building assets
.\deploy-vps.ps1 -SkipBuild

# Skip backup
.\deploy-vps.ps1 -SkipBackup

# Skip both
.\deploy-vps.ps1 -SkipBuild -SkipBackup
```

---

## 🌐 Setup Domain & SSL

### 1. Setup Nginx Virtual Host

Upload konfigurasi nginx:
```bash
scp nginx-production.conf root@145.79.12.217:/etc/nginx/sites-available/sempat-app
```

Aktifkan virtual host:
```bash
ssh root@145.79.12.217

# Aktifkan site
ln -s /etc/nginx/sites-available/sempat-app /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### 2. Install SSL Certificate (Let's Encrypt)

```bash
ssh root@145.79.12.217

# Install Certbot
apt install -y certbot python3-certbot-nginx

# Generate SSL
certbot --nginx -d your-domain.com

# Auto-renewal test
certbot renew --dry-run
```

---

## 🔄 Workflow Sehari-hari

### Deploy setelah update code:

```powershell
# 1. Test local
npm run build
php artisan test

# 2. Commit changes
git add .
git commit -m "Update feature X"

# 3. Deploy ke VPS
.\deploy-vps.ps1 -QuickDeploy
```

### Check logs di server:

```bash
ssh root@145.79.12.217

# Laravel logs
tail -f /var/www/sempat-app/storage/logs/laravel.log

# Nginx error logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Manual commands di server:

```bash
ssh root@145.79.12.217
cd /var/www/sempat-app

# Clear cache
php artisan cache:clear
php artisan config:clear

# Run migrations
php artisan migrate

# Queue worker status
supervisorctl status sempat-worker

# Restart services
systemctl restart php8.2-fpm
systemctl restart nginx
```

---

## 🐛 Troubleshooting

### Error: "Permission denied"
```bash
ssh root@145.79.12.217
cd /var/www/sempat-app
chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache
```

### Error: "Class not found"
```bash
ssh root@145.79.12.217
cd /var/www/sempat-app
composer dump-autoload
php artisan clear-compiled
php artisan config:cache
```

### Error: "Mix manifest not found"
```bash
# Rebuild assets
npm run build
.\deploy-vps.ps1 -SkipBackup
```

### Database connection error
```bash
ssh root@145.79.12.217

# Check MySQL
systemctl status mysql
mysql -u sempat_user -p sempat_lms

# Test dari Laravel
cd /var/www/sempat-app
php artisan db:show
```

---

## 📊 Monitoring & Maintenance

### Disk space check:
```bash
ssh root@145.79.12.217
df -h
du -sh /var/www/sempat-app
```

### Clear old backups:
```bash
ssh root@145.79.12.217
ls -la /var/www/ | grep backup
rm -rf /var/www/backup_20260101_120000
```

### Database backup:
```bash
ssh root@145.79.12.217
mysqldump -u sempat_user -p sempat_lms > backup_$(date +%Y%m%d).sql
```

---

## 🔐 Security Checklist

- ✅ SSH key authentication (password disabled)
- ✅ Fail2ban installed
- ✅ Firewall configured (UFW/iptables)
- ✅ SSL certificate
- ✅ APP_DEBUG=false in production
- ✅ Strong database password
- ✅ Regular backups
- ✅ Keep system updated

---

## 📞 Quick Commands

```powershell
# Setup (one time)
.\setup-ssh-key.ps1

# Deploy
.\deploy-vps.ps1

# Quick deploy
.\deploy-vps.ps1 -QuickDeploy

# SSH login
ssh root@145.79.12.217

# View logs
ssh root@145.79.12.217 "tail -f /var/www/sempat-app/storage/logs/laravel.log"

# Restart services
ssh root@145.79.12.217 "systemctl restart php8.2-fpm nginx"
```

---

## 🎯 Next Steps

1. ✅ Setup SSH key → `.\setup-ssh-key.ps1`
2. ✅ Setup server environment → Run `server-setup.sh`
3. ✅ Update .env.production
4. ✅ Deploy aplikasi → `.\deploy-vps.ps1`
5. ✅ Configure Nginx virtual host
6. ✅ Install SSL certificate
7. ✅ Test aplikasi di browser
8. ✅ Setup monitoring & backups

---

**Happy Deploying! 🚀**
