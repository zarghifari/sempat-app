# Setup Instructions for Windows + XAMPP

## 1. Create Database

### Option A: Using phpMyAdmin
1. Start XAMPP (Apache & MySQL)
2. Open browser: `http://localhost/phpmyadmin`
3. Click "New" on the left sidebar
4. Database name: `sempat_lms`
5. Collation: `utf8mb4_unicode_ci`
6. Click "Create"

### Option B: Using MySQL Command Line
```bash
# If you know your XAMPP MySQL path, run:
"C:\path\to\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE sempat_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Or use the SQL file provided:
"C:\path\to\xampp\mysql\bin\mysql.exe" -u root < create-database.sql
```

## 2. Run Migrations

After creating the database, run:

```bash
php artisan migrate
```

## 3. Generate Application Key (Already done)

```bash
php artisan key:generate
```

## 4. Install Composer Dependencies (Already done)

```bash
php composer.phar update
```

## 5. Install NPM Dependencies

```bash
npm install
```

## 6. Build Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## 7. Start Development Server

### Option A: PHP Built-in Server
```bash
php artisan serve
```
Then open: `http://localhost:8000`

### Option B: XAMPP
1. Ensure Apache is running in XAMPP
2. Access via: `http://localhost/sempat-app/public`

**Note:** You may need to configure virtual host for cleaner URLs.

## 8. Configure Virtual Host (Optional but Recommended)

### Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Add this at the end:

```apache
<VirtualHost *:80>
    ServerName sempat-lms.test
    ServerAlias www.sempat-lms.test
    DocumentRoot "D:/Ghazi/sempat-app/public"
    
    <Directory "D:/Ghazi/sempat-app/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/sempat-lms-error.log"
    CustomLog "logs/sempat-lms-access.log" common
</VirtualHost>
```

### Edit `C:\Windows\System32\drivers\etc\hosts` (Run as Administrator)

Add:
```
127.0.0.1 sempat-lms.test
127.0.0.1 www.sempat-lms.test
```

### Restart Apache

Then access via: `http://sempat-lms.test`

## 9. Storage Permissions

Ensure storage and bootstrap/cache directories are writable:

```bash
# In PowerShell (Run as Administrator if needed)
icacls storage /grant "Users:(OI)(CI)F" /T
icacls bootstrap\cache /grant "Users:(OI)(CI)F" /T
```

## 10. Queue Worker (For Background Jobs)

For development, you can run:
```bash
php artisan queue:work
```

For production on Linux, use Supervisor (see deployment docs).

## Common Issues

### Issue: "Access denied for user 'root'@'localhost'"
**Solution:** Check your MySQL password in `.env` file. Default XAMPP has no password.

### Issue: "Class not found"
**Solution:** Run `php composer.phar dump-autoload`

### Issue: "No application encryption key has been specified"
**Solution:** Run `php artisan key:generate`

### Issue: File permission errors
**Solution:** Run the icacls commands above or give write permissions to storage and bootstrap/cache folders.

## Next Steps

1. ✅ Create database `sempat_lms`
2. ✅ Run migrations: `php artisan migrate`
3. ✅ Install NPM: `npm install`
4. ✅ Test application: `php artisan serve`
5. 📚 Refer to documentation in `docs/` folder for development roadmap

## Laravel Version

This project uses **Laravel 12.46.0** with **PHP 8.4.12**.

## Compatibility Notes

This setup is designed to work on:
- ✅ Windows (development with XAMPP)
- ✅ Linux (production deployment)

The code follows Laravel best practices and is platform-independent. For Linux deployment, refer to `docs/05-Technical-Implementation-Strategy.md` Section 7.
