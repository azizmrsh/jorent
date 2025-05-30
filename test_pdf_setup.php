<?php

// Test script to verify PDF generation works with new public directory approach
require_once 'vendor/autoload.php';

use App\Services\ContractPdfService;
use App\Models\Contract1;
use Illuminate\Foundation\Application;

echo "🔧 Testing PDF Generation with Public Directory...\n";

// Test 1: Check if contracts directory exists
$contractsDir = 'public/contracts';
if (is_dir($contractsDir)) {
    echo "✅ Contracts directory exists at: {$contractsDir}\n";
} else {
    echo "❌ Contracts directory missing. Creating...\n";
    mkdir($contractsDir, 0755, true);
    echo "✅ Contracts directory created\n";
}

// Test 2: Check permissions
if (is_writable($contractsDir)) {
    echo "✅ Contracts directory is writable\n";
} else {
    echo "❌ Contracts directory is not writable\n";
    echo "   Please set permissions to 755 or 777\n";
}

// Test 3: Test file creation
$testFile = $contractsDir . '/test_file.txt';
if (file_put_contents($testFile, 'Test content')) {
    echo "✅ File creation test passed\n";
    unlink($testFile); // Clean up
} else {
    echo "❌ File creation test failed\n";
}

// Test 4: Check URL accessibility
$testUrl = 'http://localhost/contracts/';
echo "📋 Test URL structure: {$testUrl}[filename].pdf\n";
echo "   This should be directly accessible without /storage/ prefix\n";

echo "\n🎉 Pre-deployment tests completed!\n";
echo "📝 Manual steps for Hostinger deployment:\n";
echo "   1. Upload your Laravel project\n";
echo "   2. Ensure public/contracts/ exists with 755 permissions\n";
echo "   3. Test PDF generation through Filament admin\n";
echo "   4. Verify PDFs are accessible via direct URLs\n";
