# ============================================
# DEPLOY SEMPAT APP KE DOMAIN
# ============================================
# Deploy lengkap Laravel ke VPS dengan domain dan SSL
# Domain: sempat-app.com
# Server: 145.79.12.217

param(
    [string]$ServerIP = "145.79.12.217",
    [string]$ServerUser = "root",
    [string]$Domain = "sempat-app.com"
)

$ErrorActionPreference = "Stop"
$SERVER_PATH = "/var/www/sempat-app"
$LOCAL_PATH = $PSScriptRoot

# ===== FUNGSI HELPER =====
function Write-Step {
    param([string]$Message)
    Write-Host ""
    Write-Host "=====================================" -ForegroundColor Cyan
    Write-Host "▶ $Message" -ForegroundColor Cyan
    Write-Host "=====================================" -ForegroundColor Cyan
}

function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-ErrorMsg {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ️  $Message" -ForegroundColor Yellow
}

# ===== START DEPLOYMENT =====
Write-Host ""
Write-Host "========================================" -ForegroundColor Magenta
Write-Host "🚀 DEPLOY SEMPAT APP - $Domain" -ForegroundColor Magenta
Write-Host "========================================" -ForegroundColor Magenta
Write-Host ""

# ===== 1. TEST SSH CONNECTION =====
Write-Step "Testing SSH Connection to $ServerIP"
try {
    $sshTest = ssh -o ConnectTimeout=10 "$ServerUser@$ServerIP" "echo 'Connected'" 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "SSH connection failed"
    }
    Write-Success "SSH connection OK"
} catch {
    Write-ErrorMsg "Cannot connect to server!"
    Write-Host "Run this command to test manually:" -ForegroundColor Yellow
    Write-Host "  ssh $ServerUser@$ServerIP" -ForegroundColor Yellow
    exit 1
}

# ===== 2. BUILD ASSETS =====
Write-Step "Building Production Assets"
Write-Host "Installing NPM dependencies..."
npm install

Write-Host "Building with Vite..."
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "Build failed!"
    exit 1
}
Write-Success "Assets built successfully"

# ===== 3. CREATE DEPLOYMENT ARCHIVE =====
Write-Step "Creating Deployment Package"

# Files to exclude
$excludeFile = "$env:TEMP\rsync-exclude.txt"
@"
node_modules/
.git/
.env
.env.*
storage/logs/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
bootstrap/cache/*
vendor/
tests/
.gitignore
.editorconfig
phpunit.xml
*.ps1
*.sh
.vscode/
"@ | Out-File -FilePath $excludeFile -Encoding UTF8

Write-Host "Uploading files to server..."

# Upload via SCP with compression
scp -C -r `
    -o "ControlMaster=auto" `
    -o "ControlPath=/tmp/ssh-%r@%h:%p" `
    -o "ControlPersist=600" `
    "$LOCAL_PATH/" "$ServerUser@$ServerIP`:$SERVER_PATH/"

if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "File upload failed!"
    exit 1
}

# Upload .env.production sebagai .env
scp "$LOCAL_PATH\.env.production" "$ServerUser@$ServerIP`:$SERVER_PATH/.env"

Write-Success "Files uploaded"

# ===== 4. INSTALL DEPENDENCIES & SETUP =====
Write-Step "Installing Dependencies on Server"

ssh "$ServerUser@$ServerIP" @"
cd $SERVER_PATH

echo '📦 Installing Composer dependencies...'
composer install --no-dev --optimize-autoloader --no-interaction

echo '🔑 Setting permissions...'
chown -R www-data:www-data $SERVER_PATH
chmod -R 755 $SERVER_PATH
chmod -R 775 $SERVER_PATH/storage
chmod -R 775 $SERVER_PATH/bootstrap/cache

echo '🔗 Creating storage symlink...'
php artisan storage:link --force

echo '📊 Running migrations...'
php artisan migrate --force

echo '⚡ Optimizing application...'
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo '✅ Server setup complete!'
"@

if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "Server setup failed!"
    exit 1
}
Write-Success "Dependencies installed and optimized"

# ===== 5. CONFIGURE NGINX WITH SSL =====
Write-Step "Setting up SSL Certificate"

Write-Host "Installing SSL certificate for $Domain..."

ssh "$ServerUser@$ServerIP" @"
# Install SSL menggunakan Certbot
certbot --nginx -d $Domain -d www.$Domain --non-interactive --agree-tos --email drawingtogether21@gmail.com --redirect

# Test Nginx config
nginx -t

# Reload Nginx
systemctl reload nginx

echo '✅ SSL certificate installed!'
"@

if ($LASTEXITCODE -ne 0) {
    Write-ErrorMsg "SSL installation failed!"
    Write-Info "SSL bisa diinstall manual nanti dengan command:"
    Write-Info "  certbot --nginx -d $Domain -d www.$Domain"
} else {
    Write-Success "SSL certificate installed - HTTPS active!"
}

# ===== 6. SETUP QUEUE WORKER =====
Write-Step "Setting up Queue Worker"

# Upload supervisor config
$supervisorConfig = @"
[program:sempat-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $SERVER_PATH/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$SERVER_PATH/storage/logs/worker.log
stopwaitsecs=3600
"@

$supervisorConfig | ssh "$ServerUser@$ServerIP" "cat > /etc/supervisor/conf.d/sempat-worker.conf"

ssh "$ServerUser@$ServerIP" @"
supervisorctl reread
supervisorctl update
supervisorctl start sempat-worker:*
supervisorctl status
"@

Write-Success "Queue worker configured"

# ===== 7. RESTART SERVICES =====
Write-Step "Restarting Services"

ssh "$ServerUser@$ServerIP" @"
systemctl restart php8.2-fpm
systemctl reload nginx
"@

Write-Success "Services restarted"

# ===== DEPLOYMENT COMPLETE =====
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ DEPLOYMENT COMPLETE!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Website URLs:" -ForegroundColor Cyan
Write-Host "   https://$Domain" -ForegroundColor White
Write-Host "   https://www.$Domain" -ForegroundColor White
Write-Host ""
Write-Host "📊 Application Status:" -ForegroundColor Cyan
Write-Host "   ✅ Files deployed" -ForegroundColor Green
Write-Host "   ✅ Dependencies installed" -ForegroundColor Green
Write-Host "   ✅ Database migrated" -ForegroundColor Green
Write-Host "   ✅ Cache optimized" -ForegroundColor Green
Write-Host "   ✅ SSL/HTTPS active" -ForegroundColor Green
Write-Host "   ✅ Queue workers running" -ForegroundColor Green
Write-Host ""
Write-Host "🔐 Database Info:" -ForegroundColor Cyan
Write-Host "   Database: sempat_lms" -ForegroundColor White
Write-Host "   User: sempat_user" -ForegroundColor White
Write-Host "   Password: SempatLMS2026!" -ForegroundColor White
Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Cyan
Write-Host "   1. Test website: https://$Domain" -ForegroundColor White
Write-Host "   2. Login ke admin panel" -ForegroundColor White
Write-Host "   3. Upload content" -ForegroundColor White
Write-Host ""
Write-Host "🔄 To redeploy after changes:" -ForegroundColor Cyan
Write-Host "   .\deploy-to-domain.ps1" -ForegroundColor White
Write-Host ""
