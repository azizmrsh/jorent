<?php
/**
 * Emergency script to create signature directories
 * Run this on the production server to fix the signature directory issue
 */

// Navigate to the public directory
$baseDir = __DIR__ . '/public';

// Directories that need to be created
$directories = [
    'uploads',
    'uploads/contracts',
    'uploads/contracts/signatures',
    'uploads/contracts/pdfs',
    'uploads/users',
    'uploads/properties',
    'uploads/units',
    'uploads/payments',
    'uploads/payments/receipts',
    'uploads/documents',
    'uploads/temp'
];

echo "Creating signature directories...\n";

foreach ($directories as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✓ Created: $dir\n";
        } else {
            echo "✗ Failed to create: $dir\n";
        }
    } else {
        echo "- Already exists: $dir\n";
    }
}

// Create a test file to verify write permissions
$testFile = $baseDir . '/uploads/contracts/signatures/test.txt';
if (file_put_contents($testFile, 'test') !== false) {
    echo "✓ Write permission test successful\n";
    unlink($testFile); // Clean up test file
} else {
    echo "✗ Write permission test failed - check directory permissions\n";
}

echo "\nDirectory setup complete!\n";
echo "If you still have issues, check that the web server has write permissions to the uploads folder.\n";
?>
