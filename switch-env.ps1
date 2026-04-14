# ============================================
# Switch Environment - Sempat App
# ============================================
# Menyalin file .env.[environment] ke .env aktif
#
# Penggunaan:
#   .\switch-env.ps1 local       → XAMPP lokal
#   .\switch-env.ps1 staging     → VPS testing (debug on)
#   .\switch-env.ps1 production  → VPS live (debug off)

param(
    [Parameter(Mandatory = $true, Position = 0)]
    [ValidateSet('local', 'staging', 'production')]
    [string]$Environment
)

$envFile = ".env.$Environment"

if (-not (Test-Path $envFile)) {
    Write-Host "❌ File $envFile tidak ditemukan!" -ForegroundColor Red
    exit 1
}

Copy-Item $envFile .env -Force

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Environment berhasil diganti!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Environment : $Environment" -ForegroundColor Yellow

switch ($Environment) {
    'local'      { Write-Host "  App URL    : http://sempat.test" -ForegroundColor Gray }
    'staging'    { Write-Host "  App URL    : http://145.79.12.217 (debug aktif)" -ForegroundColor Gray }
    'production' { Write-Host "  App URL    : http://145.79.12.217 (debug mati)" -ForegroundColor Gray }
}

Write-Host ""

# Reminder untuk clear cache jika bukan local
if ($Environment -ne 'local') {
    Write-Host "ℹ️  Jangan lupa clear cache setelah deploy:" -ForegroundColor Yellow
    Write-Host "   php artisan config:clear && php artisan cache:clear" -ForegroundColor Gray
    Write-Host ""
}
