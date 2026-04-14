# ============================================
# FAST DEPLOY - Sempat App ke VPS
# ============================================
# Upload via compressed archive (10x lebih cepat!)
# Install dependencies di server, bukan upload

param(
    [switch]$SkipBuild = $false,
    [switch]$Staging = $false
)

# ===== KONFIGURASI =====
$SERVER_IP = "145.79.12.217"
$SERVER_USER = "root"
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

function Write-Error-Custom {
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
Write-Host "⚡ SEMPAT APP - FAST DEPLOYMENT" -ForegroundColor Magenta
Write-Host "========================================" -ForegroundColor Magenta
Write-Host ""

# Check SSH connection
Write-Step "Testing SSH Connection"
$sshTest = ssh -o BatchMode=yes -o ConnectTimeout=5 "$SERVER_USER@$SERVER_IP" "echo 'Connected'" 2>&1

if ($LASTEXITCODE -ne 0) {
    Write-Error-Custom "Cannot connect to server!"
    exit 1
}
Write-Success "SSH connection OK"

# Build assets locally
if (-not $SkipBuild) {
    Write-Step "Building Assets Locally"
    Write-Host "Building production assets..."
    npm run build
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error-Custom "Build failed!"
        exit 1
    }
    Write-Success "Assets built"
} else {
    Write-Info "Skipping build"
}

# Create deployment archive
Write-Step "Creating Deployment Package"

$timestamp = Get-Date -Format 'yyyyMMddHHmmss'
$archiveName = "sempat-deploy-$timestamp.tar.gz"
$archivePath = "$env:TEMP\$archiveName"

Write-Host "Compressing application files..."
Write-Host "Excluding: vendor/, node_modules/, tests/, .git/, logs..." -ForegroundColor Gray

# Create archive with tar (built-in Windows 10+)
$tarResult = tar --version 2>&1
if ($tarResult -match "tar") {
    tar -czf "$archivePath" `
        --exclude='.git' `
        --exclude='node_modules' `
        --exclude='vendor' `
        --exclude='tests' `
        --exclude='storage/logs/*.log' `
        --exclude='storage/framework/cache/*' `
        --exclude='storage/framework/sessions/*' `
        --exclude='storage/framework/views/*' `
        --exclude='public/storage' `
        --exclude='.env' `
        --exclude='.env.local' `
        --exclude='.env.backup*' `
        --exclude='*.md' `
        --exclude='*.ps1' `
        --exclude='*.bat' `
        --exclude='*.sh' `
        --exclude='.ssh' `
        --exclude='phpunit.xml' `
        --exclude='composer.lock' `
        -C "$LOCAL_PATH" .
    
    if ($LASTEXITCODE -ne 0) {
        Write-Error-Custom "Failed to create archive!"
        exit 1
    }
    
    $archiveSize = (Get-Item $archivePath).Length / 1MB
    Write-Success "Archive created: $([math]::Round($archiveSize, 2)) MB"
} else {
    Write-Error-Custom "tar command not found! Using fallback method..."
    # Fallback: use 7zip or manual copy
    Write-Info "Please install tar or use .\deploy-vps.ps1 instead"
    exit 1
}

# Upload archive to server
Write-Step "Uploading to Server"

Write-Host "Uploading $archiveName..."
scp -C "$archivePath" "$SERVER_USER@$SERVER_IP`:/tmp/$archiveName"

if ($LASTEXITCODE -ne 0) {
    Write-Error-Custom "Upload failed!"
    Remove-Item $archivePath -Force -ErrorAction SilentlyContinue
    exit 1
}

Write-Success "Upload complete"

# Clean local archive
Remove-Item $archivePath -Force

# Extract and setup on server - all commands in ONE SSH session
Write-Step "Extracting and Setting Up on Server"

Write-Host "Running deployment commands on server..."

# Write all commands to a temp script and execute via single SSH session
# This avoids interactive prompts from stdin inheritance
$serverScript = @"
#!/bin/bash
set -e

echo '📦 Extracting archive...'
mkdir -p $SERVER_PATH
cd $SERVER_PATH && tar -xzf /tmp/$archiveName
rm -f /tmp/$archiveName

echo '📝 Setting up .env...'
cd $SERVER_PATH
if [ -f .env.$($Staging ? 'staging' : 'production') ]; then cp .env.$($Staging ? 'staging' : 'production') .env; fi

echo '📦 Installing Composer dependencies...'
cd $SERVER_PATH && COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs 2>&1 || true

echo '📦 Installing NPM dependencies...'
cd $SERVER_PATH && npm ci --omit=dev --no-fund --no-audit 2>&1

echo '🔑 Checking app key...'
cd $SERVER_PATH
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
  php artisan key:generate --force 2>&1 || true
fi

echo '🧹 Clearing caches...'
cd $SERVER_PATH && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

echo '🗄️  Running migrations...'
cd $SERVER_PATH && php artisan migrate --force

echo '🔗 Creating storage link...'
cd $SERVER_PATH
# Fix: remove directory if not a symlink, then recreate
if [ -d public/storage ] && [ ! -L public/storage ]; then
  echo "  Removing non-symlink public/storage directory..."
  rm -rf public/storage
fi
php artisan storage:link 2>&1 || true

echo '⚡ Optimizing for production...'
cd $SERVER_PATH && php artisan config:cache && php artisan route:cache && php artisan view:cache

echo '📁 Setting permissions...'
chown -R www-data:www-data $SERVER_PATH
chmod -R 755 $SERVER_PATH
chmod -R 775 $SERVER_PATH/storage
chmod -R 775 $SERVER_PATH/bootstrap/cache

echo '🔄 Restarting services...'
systemctl restart php8.2-fpm
systemctl restart nginx

echo '✅ Deployment complete!'
"@

# Execute all commands in one SSH session - no stdin inheritance
$serverScript | ssh -T -o BatchMode=yes "$SERVER_USER@$SERVER_IP" "bash -s"

if ($LASTEXITCODE -ne 0) {
    Write-Error-Custom "Server setup failed!"
    exit 1
}

Write-Success "Server setup completed"

# Final status
Write-Step "Deployment Summary"

$statusCmd = @"
cd $SERVER_PATH && \
echo "Application Path: $SERVER_PATH" && \
echo "" && \
echo "PHP Version:" && php -v | head -n 1 && \
echo "" && \
echo "Laravel Version:" && php artisan --version && \
echo "" && \
echo "Disk Usage:" && du -sh $SERVER_PATH
"@

ssh "$SERVER_USER@$SERVER_IP" $statusCmd

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ DEPLOYMENT SUCCESSFUL!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Access your application at:" -ForegroundColor Yellow
Write-Host "  http://$SERVER_IP" -ForegroundColor Cyan
Write-Host ""
Write-Host "Quick redeploy:" -ForegroundColor Yellow
Write-Host "  .\deploy-fast.ps1" -ForegroundColor Gray
Write-Host "  .\deploy-fast.ps1 -SkipBuild" -ForegroundColor Gray
Write-Host ""
