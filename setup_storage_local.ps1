# Laravel Local Storage Setup Script
# Run this locally before deploying

Write-Host "🔧 Setting up Laravel storage structure locally..." -ForegroundColor Green

# Create required directories
$directories = @(
    "storage\app\public\profile_photos",
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\testing", 
    "storage\framework\views",
    "storage\logs",
    "bootstrap\cache"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "✅ Created: $dir" -ForegroundColor Green
    } else {
        Write-Host "✅ Already exists: $dir" -ForegroundColor Yellow
    }
}

# Create .gitkeep files
$gitkeepDirs = @(
    "storage\app",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\testing",
    "storage\framework\views",
    "storage\logs"
)

foreach ($dir in $gitkeepDirs) {
    $gitkeepPath = "$dir\.gitkeep"
    if (!(Test-Path $gitkeepPath)) {
        New-Item -ItemType File -Path $gitkeepPath -Force | Out-Null
        Write-Host "✅ Created .gitkeep in: $dir" -ForegroundColor Green
    }
}

# Clear and rebuild caches
Write-Host "🔄 Clearing Laravel caches..." -ForegroundColor Blue
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear

Write-Host "🔄 Rebuilding Laravel caches..." -ForegroundColor Blue
php artisan config:cache
php artisan view:cache
php artisan route:cache

# Create storage link
Write-Host "🔗 Creating storage symlink..." -ForegroundColor Blue
php artisan storage:link

Write-Host ""
Write-Host "🎉 Local setup complete!" -ForegroundColor Green
Write-Host "📁 All storage directories created" -ForegroundColor Green  
Write-Host "🔗 Storage symlink created" -ForegroundColor Green
Write-Host "💾 Laravel caches optimized" -ForegroundColor Green
Write-Host ""
Write-Host "Next: Upload the fix_storage_permissions.php to your server and run it!" -ForegroundColor Yellow
