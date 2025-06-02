<?php
/**
 * Laravel Storage Fix Script for Hostinger/cPanel
 * Fixes: file_put_contents failed to open stream errors
 * Usage: Upload to server root and run via browser or terminal
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🔧 Laravel Storage & Permission Fix Script\n";
echo "==========================================\n";
echo "Target: jorent.eva-adam.com\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$basePath = __DIR__;
$storagePath = $basePath . '/storage';

echo "Base Path: $basePath\n";
echo "Storage Path: $storagePath\n\n";

// Check if we're in the right directory
if (!file_exists($basePath . '/artisan')) {
    die("❌ Error: artisan file not found. Make sure this script is in Laravel root directory.\n");
}

echo "✅ Laravel installation detected\n\n";

// Required directories
$directories = [
    'storage/app',
    'storage/app/public',
    'storage/app/public/profile_photos',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
    'public/storage'
];

// Create directories
foreach ($directories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created: $dir\n";
        } else {
            echo "❌ Failed to create: $dir\n";
        }
    } else {
        echo "✅ Already exists: $dir\n";
    }
    
    // Set permissions
    if (is_dir($fullPath)) {
        chmod($fullPath, 0755);
        echo "✅ Set permissions for: $dir\n";
    }
}

// Create symbolic link for storage
$publicStorage = $basePath . '/public/storage';
$appPublic = $basePath . '/storage/app/public';

if (!file_exists($publicStorage) && is_dir($appPublic)) {
    if (symlink($appPublic, $publicStorage)) {
        echo "✅ Created storage symlink\n";
    } else {
        echo "❌ Failed to create storage symlink\n";
    }
}

// Create .gitkeep files
$gitkeepDirs = [
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs'
];

foreach ($gitkeepDirs as $dir) {
    $gitkeepPath = $basePath . '/' . $dir . '/.gitkeep';
    if (!file_exists($gitkeepPath)) {
        file_put_contents($gitkeepPath, '');
        echo "✅ Created .gitkeep in: $dir\n";
    }
}

echo "\n🎉 Storage structure setup complete!\n";
echo "==================================\n";

// Test directory permissions
echo "\n📋 Testing directory permissions:\n";
$testDirs = ['storage/framework/cache', 'storage/app/public', 'bootstrap/cache'];
foreach ($testDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (is_writable($fullPath)) {
        echo "✅ $dir - Writable\n";
    } else {
        echo "❌ $dir - Not writable\n";
    }
}

// Run Laravel commands
echo "\n🔄 Running Laravel commands:\n";
$commands = [
    'config:clear' => 'Clear config cache',
    'cache:clear' => 'Clear application cache', 
    'view:clear' => 'Clear view cache',
    'route:clear' => 'Clear route cache'
];

foreach ($commands as $cmd => $desc) {
    echo "Running: php artisan $cmd ($desc)\n";
    $output = shell_exec("cd $basePath && php artisan $cmd 2>&1");
    echo "Result: " . trim($output) . "\n";
}

echo "\n✅ Fix script completed successfully!\n";
echo "Next steps:\n";
echo "1. Test profile photo upload in admin panel\n";
echo "2. Check storage/app/public/profile_photos/ for uploaded files\n";
echo "3. If issues persist, contact hosting support\n";
echo "\n📧 Contact: Technical Team\n";
