# 🚀 JORENT DEPLOYMENT SCRIPT - PowerShell Version
# For Windows servers or PowerShell environments

Write-Host "🚀 Starting Jorent Deployment..." -ForegroundColor Green

# Step 1: Pull latest changes
Write-Host "📥 Pulling latest changes from GitHub..." -ForegroundColor Yellow
git checkout test
git pull origin test

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Git pull failed!" -ForegroundColor Red
    exit 1
}

# Step 2: Remove problematic package
Write-Host "🗑️ Removing problematic package..." -ForegroundColor Yellow
composer remove hydrat/filament-table-layout-toggle --no-dev

if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️ Package might already be removed, continuing..." -ForegroundColor Yellow
}

# Step 3: Update autoloader
Write-Host "🔄 Updating composer autoloader..." -ForegroundColor Yellow
composer dump-autoload

# Step 4: Clear all caches
Write-Host "🧹 Clearing all Laravel caches..." -ForegroundColor Yellow
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Step 5: Verify Laravel is working
Write-Host "✅ Verifying Laravel installation..." -ForegroundColor Yellow
$version = php artisan --version
Write-Host "Laravel Version: $version" -ForegroundColor Green

Write-Host "🎉 Deployment completed successfully!" -ForegroundColor Green
Write-Host "🌐 Please test your website: https://jorent.eva-adam.com" -ForegroundColor Cyan
Write-Host "🔑 Admin login: https://jorent.eva-adam.com/admin" -ForegroundColor Cyan
Write-Host "📧 Username: admin@jorent.com" -ForegroundColor Cyan
Write-Host "🔒 Password: admin123456" -ForegroundColor Cyan
