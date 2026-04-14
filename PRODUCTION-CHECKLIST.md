# ============================================
# Production Optimization Checklist
# ============================================
# Setup yang perlu dilakukan untuk production-ready deployment

## ✅ SUDAH CONFIGURED

### Environment
- [x] APP_ENV=production
- [x] APP_DEBUG=false
- [x] APP_URL=http://145.79.12.217
- [x] ASSET_URL=http://145.79.12.217

### Database
- [x] DB_CONNECTION=mysql
- [x] DB_HOST=localhost (sudah benar, karena MySQL di server yang sama)
- [x] Database sempat_lms created
- [x] Migrations executed

### Session & Cache
- [x] SESSION_DRIVER=database (production-ready)
- [x] CACHE_STORE=database (production-ready)
- [x] Config cached (php artisan config:cache)
- [x] Routes cached (php artisan route:cache)
- [x] Views cached (php artisan view:cache)

### File Storage
- [x] Storage link created (public/storage -> storage/app/public)
- [x] Storage permissions correct (775)
- [x] Public disk URL: http://145.79.12.217/storage

### Server
- [x] PHP 8.2.30
- [x] Composer dependencies installed
- [x] NPM dependencies installed  
- [x] Assets built (Vite)
- [x] Nginx configured
- [x] PHP-FPM running
- [x] Permissions set (www-data:www-data)

---

## ⚙️ PERLU SETUP

### 1. Queue Worker (untuk Document Import background processing)

Queue worker belum running. Install supervisor config:

```bash
# Di server
ssh root@145.79.12.217

# Upload supervisor config
# File sudah ada di: supervisor-worker.conf

# Copy ke supervisor
sudo cp /var/www/sempat-app/supervisor-worker.conf /etc/supervisor/conf.d/sempat-worker.conf

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sempat-worker:*

# Check status
sudo supervisorctl status
```

Atau run otomatis:
```powershell
.\setup-queue-worker.ps1
```

### 2. Email Configuration (Optional - untuk password reset, notifications)

Saat ini MAIL_MAILER=log (emails hanya di-log, tidak dikirim).

**Untuk production real, gunakan SMTP service:**

Update di .env.production:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com  # atau SMTP provider lain
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Atau gunakan service seperti:
- **Mailgun** (https://mailgun.com) - 5000 emails/month free
- **SendGrid** (https://sendgrid.com) - 100 emails/day free
- **Amazon SES** - murah, ~$0.10 per 1000 emails

### 3. Redis (Optional - untuk better caching & session)

Install Redis untuk performance boost:

```bash
ssh root@145.79.12.217

# Redis sudah installed, tinggal enable
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Update .env
nano /var/www/sempat-app/.env
# Change:
# CACHE_STORE=redis
# SESSION_DRIVER=redis

# Clear cache
cd /var/www/sempat-app
php artisan config:clear
php artisan config:cache
```

Update .env.production lokal:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 4. Scheduled Tasks (Cron)

Laravel punya scheduled tasks untuk maintenance. Setup cron:

```bash
ssh root@145.79.12.217

# Edit crontab
crontab -e

# Add line:
* * * * * cd /var/www/sempat-app && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Backup Automation (PENTING!)

Setup automated database backup:

```bash
ssh root@145.79.12.217

# Create backup script
nano /root/backup-sempat.sh
```

Paste:
```bash
#!/bin/bash
BACKUP_DIR="/root/backups/sempat"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u sempat_user -pSempatLMS2026! sempat_lms > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/sempat-app/storage/app/public

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

Make executable & add to cron:
```bash
chmod +x /root/backup-sempat.sh

# Crontab: backup setiap hari jam 2 pagi
crontab -e
# Add:
0 2 * * * /root/backup-sempat.sh >> /var/log/sempat-backup.log 2>&1
```

### 6. Monitoring & Logging

**Log rotation:**
```bash
ssh root@145.79.12.217

# Laravel log rotation
nano /etc/logrotate.d/sempat-app
```

Paste:
```
/var/www/sempat-app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0775 www-data www-data
    sharedscripts
}
```

**Log monitoring:**
```bash
# Check Laravel logs
tail -f /var/www/sempat-app/storage/logs/laravel.log

# Check Nginx logs
tail -f /var/log/nginx/error.log

# Check queue worker logs
tail -f /var/www/sempat-app/storage/logs/worker.log
```

### 7. Security Hardening

**Disable password SSH login (sudah pakai key):**
```bash
ssh root@145.79.12.217
nano /etc/ssh/sshd_config

# Ensure:
# PasswordAuthentication no
# PermitRootLogin prohibit-password

systemctl restart sshd
```

**Setup firewall (UFW):**
```bash
ssh root@145.79.12.217

# Install UFW
apt install ufw -y

# Allow SSH first!
ufw allow 22/tcp

# Allow HTTP/HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Enable
ufw --force enable

# Check status
ufw status
```

### 8. Performance Optimization

**OPcache (PHP optimization):**
```bash
ssh root@145.79.12.217

# Edit PHP-FPM config
nano /etc/php/8.2/fpm/php.ini

# Enable OPcache:
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0  # production only
opcache.revalidate_freq=0

# Restart
systemctl restart php8.2-fpm
```

**MySQL optimization:**
```bash
ssh root@145.79.12.217
nano /etc/mysql/my.cnf

# Add under [mysqld]:
innodb_buffer_pool_size=512M
innodb_log_file_size=256M
max_connections=200

# Restart
systemctl restart mysql
```

---

## 🚀 QUICK DEPLOYMENT COMMANDS

### Deploy update code:
```powershell
.\deploy-fast.ps1 -SkipBuild
```

### Deploy with full build:
```powershell
.\deploy-fast.ps1
```

### Check application status:
```bash
ssh root@145.79.12.217 "cd /var/www/sempat-app && php artisan about"
```

### Restart services:
```bash
ssh root@145.79.12.217 "systemctl restart php8.2-fpm nginx && supervisorctl restart sempat-worker:*"
```

### Clear all caches:
```bash
ssh root@145.79.12.217 "cd /var/www/sempat-app && php artisan optimize:clear"
```

### Re-optimize for production:
```bash
ssh root@145.79.12.217 "cd /var/www/sempat-app && php artisan optimize"
```

---

## 📊 MONITORING CHECKLIST

Daily checks:
- [ ] Application accessible: http://145.79.12.217
- [ ] Check error logs: `tail -f storage/logs/laravel.log`
- [ ] Queue worker running: `supervisorctl status`
- [ ] Disk space: `df -h`
- [ ] Database size: `du -sh /var/lib/mysql`

Weekly checks:
- [ ] Review access logs untuk unusual activity
- [ ] Check backup files exist dan bisa di-restore
- [ ] Update system: `apt update && apt upgrade`
- [ ] Check failed jobs: `SELECT * FROM failed_jobs;`

---

## 🎯 CURRENT STATUS

```
✅ Production Ready
✅ Database configured
✅ Assets optimized  
✅ Storage linked
✅ Permissions set
✅ Nginx running
✅ SSL ready (setelah domain)

⚠️  TO DO:
- Setup queue worker (supervisor)
- Configure email (optional)
- Setup automated backups
- Add cron for scheduled tasks
- Enable Redis (optional performance boost)
```

---

**Application is LIVE and functional!**
**Remaining items are for optimization and maintenance.**
