#!/bin/bash

# Laravel Storage Permission Fix Script
# Upload this to your hosting server and run it

echo "🔧 Laravel Storage Permission Fix"
echo "================================="

# Set proper permissions for Laravel directories
echo "Setting directory permissions..."

# Storage directories
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/storage/

# Create missing directories if they don't exist
mkdir -p storage/app/public/profile_photos
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set correct permissions
chmod 755 storage/app/public/profile_photos
chmod 755 storage/framework/cache
chmod 755 storage/framework/cache/data
chmod 755 storage/framework/sessions
chmod 755 storage/framework/testing
chmod 755 storage/framework/views
chmod 755 storage/logs
chmod 755 bootstrap/cache

# Create storage link if it doesn't exist
if [ ! -L "public/storage" ]; then
    ln -s ../storage/app/public public/storage
    echo "✅ Created storage symlink"
fi

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Recreate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Storage permissions fixed!"
echo "✅ Laravel caches cleared and regenerated!"
echo ""
echo "🎉 Your application should now work properly!"
