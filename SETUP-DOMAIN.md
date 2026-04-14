# 🌐 Cara Menambahkan Domain ke VPS

Ketika Anda sudah membeli domain, ikuti langkah-langkah ini untuk menghubungkan domain ke VPS.

---

## 📋 Pre-requisites

✅ Domain sudah dibeli (contoh: sempat.com)  
✅ Akses ke DNS management (Cloudflare, Hostinger, atau registrar lain)  
✅ Aplikasi sudah di-deploy ke VPS  

---

## 1️⃣ Point Domain ke VPS

### Option A: Cloudflare (Recommended)

1. **Login ke Cloudflare**

2. **Add Site** → masukkan domain Anda

3. **DNS Records** → Tambahkan:
   ```
   Type: A
   Name: @
   IPv4: 145.79.12.217
   Proxy: OFF (orange cloud OFF) dulu, nanti bisa di-ON setelah SSL
   
   Type: A
   Name: www
   IPv4: 145.79.12.217
   Proxy: OFF
   ```

4. **Update Nameservers** di registrar domain Anda ke nameservers Cloudflare

5. **Tunggu DNS propagation** (~5-30 menit, kadang bisa sampai 24 jam)

### Option B: DNS Registrar Langsung

Di control panel domain registrar (Hostinger/GoDaddy/Niagahoster):

1. **DNS Management** → Tambahkan A Record:
   ```
   Host: @
   Type: A
   Value: 145.79.12.217
   TTL: 1 hour
   
   Host: www
   Type: A
   Value: 145.79.12.217
   TTL: 1 hour
   ```

2. **Tunggu DNS propagation**

### Cek DNS Sudah Propagate

```powershell
# Windows
nslookup your-domain.com

# Atau online
# https://dnschecker.org
```

---

## 2️⃣ Update .env.production

Edit [.env.production](.env.production):

```env
APP_URL=https://your-domain.com
```

Deploy ulang:
```powershell
.\deploy-vps.ps1 -QuickDeploy
```

---

## 3️⃣ Update Nginx Configuration

### Upload config SSL:

```powershell
scp nginx-production-ssl.conf root@145.79.12.217:/etc/nginx/sites-available/sempat-app
```

### Atau manual edit:

```bash
ssh root@145.79.12.217
nano /etc/nginx/sites-available/sempat-app
```

Ganti `server_name` dan path SSL certificate:

```nginx
server_name your-domain.com www.your-domain.com;

ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
```

Test & reload:
```bash
nginx -t
systemctl reload nginx
```

---

## 4️⃣ Install SSL Certificate (Let's Encrypt)

```bash
ssh root@145.79.12.217

# Install Certbot
apt install -y certbot python3-certbot-nginx

# Generate SSL untuk domain
certbot --nginx -d your-domain.com -d www.your-domain.com

# Akan diminta email, setuju ToS
# Certbot otomatis update Nginx config dan reload
```

### Test auto-renewal:

```bash
certbot renew --dry-run
```

### Manual renewal (jika perlu):

```bash
certbot renew
systemctl reload nginx
```

---

## 5️⃣ Test Domain

```bash
# Akses di browser
https://your-domain.com

# Check SSL
https://www.ssllabs.com/ssltest/
```

---

## 6️⃣ Enable Cloudflare Proxy (Optional)

Jika pakai Cloudflare, setelah SSL aktif:

1. **Cloudflare Dashboard** → DNS
2. **Toggle orange cloud** untuk A records (@ dan www)
3. **SSL/TLS** → Set mode: **Full (strict)**
4. **Speed** → Enable auto minify, Brotli, etc.
5. **Security** → Setup firewall rules, rate limiting

---

## 🔄 Rollback ke IP (Jika Ada Masalah)

Jika ada masalah dengan domain:

1. **Edit .env.production**:
   ```env
   APP_URL=http://145.79.12.217
   ```

2. **Restore Nginx config**:
   ```bash
   ssh root@145.79.12.217
   cp /etc/nginx/sites-available/sempat-app.backup /etc/nginx/sites-available/sempat-app
   nginx -t && systemctl reload nginx
   ```

3. **Deploy:**
   ```powershell
   .\deploy-vps.ps1 -QuickDeploy
   ```

---

## 📝 Checklist Setelah Domain Aktif

- [ ] Domain sudah point ke IP VPS
- [ ] DNS propagation selesai (bisa resolve)
- [ ] .env.production updated dengan APP_URL domain
- [ ] Nginx virtual host updated
- [ ] SSL certificate installed
- [ ] HTTPS redirect dari HTTP aktif
- [ ] Test akses https://domain.com
- [ ] Test akses https://www.domain.com
- [ ] SSL grade A/A+ (SSLLabs)
- [ ] Cloudflare proxy enabled (optional)

---

## 🆘 Troubleshooting

### Domain tidak bisa diakses

```bash
# Check DNS
nslookup your-domain.com

# Check Nginx
ssh root@145.79.12.217
systemctl status nginx
nginx -t
tail -f /var/log/nginx/error.log
```

### SSL error

```bash
# Re-generate certificate
certbot delete
certbot --nginx -d your-domain.com -d www.your-domain.com

# Check certificate
certbot certificates
```

### Mixed content error

Update .env.production:
```env
APP_URL=https://your-domain.com  # pastikan HTTPS
```

Deploy ulang:
```powershell
.\deploy-vps.ps1 -QuickDeploy
```

---

## 💡 Tips

1. **Gunakan Cloudflare** untuk:
   - DDoS protection
   - CDN gratis
   - Analytics
   - Cache
   - Always Online

2. **Setup monitoring**:
   - UptimeRobot (free uptime monitoring)
   - Cloudflare Analytics

3. **Backup**:
   - Setup automated backups
   - Backup database harian
   - Backup files mingguan

---

**Current Status:**
```
✅ Akses via IP: http://145.79.12.217
❌ Domain: Belum setup
❌ SSL: Belum setup
```

**Setelah punya domain, ikuti langkah 1-6 di atas!**
