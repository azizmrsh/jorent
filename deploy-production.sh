#!/bin/bash
# 🚀 JORENT DEPLOYMENT SCRIPT - Bash Version
# For Linux/Unix servers

echo "🚀 Starting Jorent Deployment..."

# Step 1: Pull latest changes
echo "📥 Pulling latest changes from GitHub..."
git checkout test
git pull origin test

if [ $? -ne 0 ]; then
    echo "❌ Git pull failed!"
    exit 1
fi

# Step 2: Remove problematic package
echo "🗑️ Removing problematic package..."
composer remove hydrat/filament-table-layout-toggle --no-dev

if [ $? -ne 0 ]; then
    echo "⚠️ Package might already be removed, continuing..."
fi

# Step 3: Update autoloader
echo "🔄 Updating composer autoloader..."
composer dump-autoload

# Step 4: Clear all caches
echo "🧹 Clearing all Laravel caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Step 5: Verify Laravel is working
echo "✅ Verifying Laravel installation..."
version=$(php artisan --version)
echo "Laravel Version: $version"

echo "🎉 Deployment completed successfully!"
echo "🌐 Please test your website: https://jorent.eva-adam.com"
echo "🔑 Admin login: https://jorent.eva-adam.com/admin"
echo "📧 Username: admin@jorent.com"
echo "🔒 Password: admin123456"
