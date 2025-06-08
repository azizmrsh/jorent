<?php

echo "=== Simple Upload System Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if upload directories exist and are writable
$uploadDirs = [
    'public/uploads',
    'public/uploads/users',
    'public/uploads/tenants',
    'public/uploads/accs',
    'public/uploads/properties',
    'public/uploads/units',
    'public/uploads/contracts',
    'public/uploads/contracts/signatures',
    'public/uploads/contracts/pdfs'
];

echo "1. Testing Directory Existence and Permissions:\n";
echo str_repeat("-", 60) . "\n";

foreach ($uploadDirs as $dir) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    
    echo sprintf("%-35s | %-6s | %-8s\n", 
        $dir, 
        $exists ? 'EXISTS' : 'MISSING', 
        $writable ? 'WRITABLE' : 'NO WRITE'
    );
}

echo "\n2. Testing File Creation:\n";
echo str_repeat("-", 60) . "\n";

$testsPassed = 0;
$testsTotal = 0;

foreach ($uploadDirs as $dir) {
    $testsTotal++;
    if (is_dir($dir) && is_writable($dir)) {
        $testFile = $dir . '/test_' . time() . '.txt';
        $success = file_put_contents($testFile, 'Test file content - ' . date('Y-m-d H:i:s'));
        
        if ($success) {
            echo "✓ $dir - File creation SUCCESS\n";
            $testsPassed++;
            // Clean up
            unlink($testFile);
        } else {
            echo "✗ $dir - File creation FAILED\n";
        }
    } else {
        echo "✗ $dir - Directory not writable or missing\n";
    }
}

echo "\n3. Configuration Files Check:\n";
echo str_repeat("-", 60) . "\n";

$configFiles = [
    'config/filesystems.php' => 'Laravel filesystems config',
    'app/Traits/FileUploadTrait.php' => 'File upload trait',
    'app/Services/FileUploadService.php' => 'File upload service'
];

foreach ($configFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description exists\n";
    } else {
        echo "✗ $description missing\n";
    }
}

echo "\n=== TEST SUMMARY ===\n";
echo "Directories tested: $testsTotal\n";
echo "Successful writes: $testsPassed\n";
echo "Success rate: " . round(($testsPassed / $testsTotal) * 100, 1) . "%\n";

if ($testsPassed === $testsTotal) {
    echo "\n🎉 ALL TESTS PASSED! Upload system is ready.\n";
} else {
    echo "\n⚠️  Some tests failed. Check directory permissions.\n";
}

echo "\nNext steps:\n";
echo "- Test actual file uploads through the admin interface\n";
echo "- Verify uploaded files display correctly\n";
echo "- Test signature functionality in contracts\n";

?>
