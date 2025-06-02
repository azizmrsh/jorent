<?php
/**
 * Quick Test Script for Laravel Storage
 * Run this to verify storage permissions are working
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🧪 Laravel Storage Test Script\n";
echo "=============================\n";
echo "Testing storage functionality for photo uploads...\n\n";

$basePath = __DIR__;

// Test 1: Check if Laravel is detected
echo "Test 1: Laravel Detection\n";
if (file_exists($basePath . '/artisan')) {
    echo "✅ Laravel detected (artisan file found)\n";
} else {
    echo "❌ Laravel not detected\n";
    exit(1);
}

// Test 2: Check storage directories
echo "\nTest 2: Storage Directories\n";
$requiredDirs = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/app/public/profile_photos',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
    'public/storage'
];

foreach ($requiredDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (is_dir($fullPath)) {
        echo "✅ $dir exists\n";
    } else {
        echo "❌ $dir missing\n";
    }
}

// Test 3: Check write permissions
echo "\nTest 3: Write Permissions\n";
$writableDirs = [
    'storage/framework/cache',
    'storage/app/public/profile_photos',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($writableDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (is_writable($fullPath)) {
        echo "✅ $dir is writable\n";
    } else {
        echo "❌ $dir is not writable\n";
    }
}

// Test 4: Test file creation
echo "\nTest 4: File Creation Test\n";
$testFile = $basePath . '/storage/app/public/profile_photos/test_upload.txt';
try {
    if (file_put_contents($testFile, 'Test upload: ' . date('Y-m-d H:i:s'))) {
        echo "✅ File creation successful\n";
        unlink($testFile); // Clean up
        echo "✅ File deletion successful\n";
    } else {
        echo "❌ File creation failed\n";
    }
} catch (Exception $e) {
    echo "❌ File creation error: " . $e->getMessage() . "\n";
}

// Test 5: Check symlink
echo "\nTest 5: Storage Symlink\n";
$publicStorage = $basePath . '/public/storage';
if (is_link($publicStorage)) {
    echo "✅ Storage symlink exists\n";
    $target = readlink($publicStorage);
    echo "   → Points to: $target\n";
} else {
    echo "❌ Storage symlink missing\n";
}

// Test 6: PHP Configuration
echo "\nTest 6: PHP Configuration\n";
echo "Upload Max Size: " . ini_get('upload_max_filesize') . "\n";
echo "Post Max Size: " . ini_get('post_max_size') . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";

echo "\n🎯 Test Summary\n";
echo "==============\n";
echo "If all tests show ✅, photo upload should work.\n";
echo "If any test shows ❌, that needs to be fixed.\n";
echo "\nTo fix issues:\n";
echo "1. Run fix_storage_permissions.php\n";
echo "2. Contact hosting support if permissions can't be set\n";
echo "3. Check hosting control panel for file manager\n";

?>
