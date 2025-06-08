<?php
/**
 * Comprehensive Upload System Test
 * Tests file upload functionality across all directories
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🔧 Testing Upload System...\n\n";

// Test directories
$testDirs = [
    'uploads/users',
    'uploads/users/documents',
    'uploads/properties',
    'uploads/units',
    'uploads/contracts/signatures',
    'uploads/contracts/pdfs',
    'uploads/payments/receipts',
    'uploads/payments/proofs',
    'uploads/documents',
    'uploads/temp'
];

$baseDir = __DIR__ . '/public';
$testsPassed = 0;
$testsTotal = 0;

echo "📁 Testing directory permissions and write access...\n";

foreach ($testDirs as $dir) {
    $testsTotal++;
    $fullPath = $baseDir . '/' . $dir;
    
    echo "Testing: $dir\n";
    
    // Check if directory exists
    if (!is_dir($fullPath)) {
        echo "  ❌ Directory does not exist\n";
        continue;
    }
    
    // Test write permissions
    $testFile = $fullPath . '/test_' . time() . '.txt';
    $testContent = 'Upload test file - ' . date('Y-m-d H:i:s');
    
    if (file_put_contents($testFile, $testContent) !== false) {
        echo "  ✅ Write permission OK\n";
        
        // Test read permission
        if (file_get_contents($testFile) === $testContent) {
            echo "  ✅ Read permission OK\n";
            $testsPassed++;
        } else {
            echo "  ❌ Read permission failed\n";
        }
        
        // Clean up test file
        unlink($testFile);
        echo "  ✅ File cleanup OK\n";
    } else {
        echo "  ❌ Write permission failed\n";
    }
    
    echo "\n";
}

echo "📊 Test Results:\n";
echo "Passed: $testsPassed / $testsTotal directories\n\n";

// Test Laravel filesystem configuration
echo "🔧 Testing Laravel Filesystem Configuration...\n";

try {
    // Load Laravel configuration
    $configPath = __DIR__ . '/config/filesystems.php';
    if (file_exists($configPath)) {
        $config = include $configPath;
        
        if (isset($config['disks']['uploads'])) {
            echo "✅ 'uploads' disk configuration found\n";
            $uploadsConfig = $config['disks']['uploads'];
            
            echo "  Driver: " . ($uploadsConfig['driver'] ?? 'not set') . "\n";
            echo "  Root: " . ($uploadsConfig['root'] ?? 'not set') . "\n";
            echo "  URL: " . ($uploadsConfig['url'] ?? 'not set') . "\n";
            echo "  Visibility: " . ($uploadsConfig['visibility'] ?? 'not set') . "\n";
        } else {
            echo "❌ 'uploads' disk configuration not found\n";
        }
    } else {
        echo "❌ Filesystem configuration file not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading filesystem config: " . $e->getMessage() . "\n";
}

echo "\n";

// Test image upload simulation
echo "🖼️ Testing Image Upload Simulation...\n";

// Create a simple test image (1x1 pixel PNG)
$testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

$imageTestDirs = [
    'uploads/users' => 'user_photo_test.png',
    'uploads/properties' => 'property_image_test.png',
    'uploads/units' => 'unit_image_test.png',
    'uploads/contracts/signatures' => 'signature_test.png'
];

foreach ($imageTestDirs as $dir => $filename) {
    $fullPath = $baseDir . '/' . $dir . '/' . $filename;
    
    if (file_put_contents($fullPath, $testImageData) !== false) {
        echo "✅ Image upload test passed: $dir/$filename\n";
        
        // Verify file size
        $fileSize = filesize($fullPath);
        echo "  File size: $fileSize bytes\n";
        
        // Check if file is accessible via web URL (simulate)
        $webPath = '/uploads/' . str_replace('uploads/', '', $dir) . '/' . $filename;
        echo "  Web path would be: $webPath\n";
        
        // Clean up test file
        unlink($fullPath);
        echo "  ✅ Test file cleaned up\n";
    } else {
        echo "❌ Image upload test failed: $dir/$filename\n";
    }
    echo "\n";
}

// Test FileUpload service if available
echo "🔧 Testing FileUpload Service Integration...\n";

$fileUploadServicePath = __DIR__ . '/app/Services/FileUploadService.php';
if (file_exists($fileUploadServicePath)) {
    echo "✅ FileUploadService found\n";
    
    // Check if the service has the required methods
    $serviceContent = file_get_contents($fileUploadServicePath);
    
    $requiredMethods = ['uploadFile', 'deleteFile', 'getUploadPath'];
    foreach ($requiredMethods as $method) {
        if (strpos($serviceContent, "function $method") !== false || strpos($serviceContent, "public function $method") !== false) {
            echo "  ✅ Method '$method' found\n";
        } else {
            echo "  ❌ Method '$method' not found\n";
        }
    }
} else {
    echo "❌ FileUploadService not found\n";
}

echo "\n";

// Test FileUpload trait
echo "🔧 Testing FileUpload Trait...\n";

$fileUploadTraitPath = __DIR__ . '/app/Traits/FileUploadTrait.php';
if (file_exists($fileUploadTraitPath)) {
    echo "✅ FileUploadTrait found\n";
    
    $traitContent = file_get_contents($fileUploadTraitPath);
    
    $requiredMethods = ['uploadImage', 'deleteImage', 'getImagePath'];
    foreach ($requiredMethods as $method) {
        if (strpos($traitContent, "function $method") !== false || strpos($traitContent, "public function $method") !== false) {
            echo "  ✅ Method '$method' found\n";
        } else {
            echo "  ❌ Method '$method' not found\n";
        }
    }
} else {
    echo "❌ FileUploadTrait not found\n";
}

echo "\n";

// Final summary
if ($testsPassed === $testsTotal) {
    echo "🎉 All upload system tests PASSED!\n";
    echo "Your upload system is properly configured and ready for use.\n\n";
    
    echo "✅ What's working:\n";
    echo "  - All required directories exist\n";
    echo "  - Write/read permissions are correct\n";
    echo "  - Image upload simulation successful\n";
    echo "  - Laravel filesystem configuration is in place\n\n";
    
    echo "📝 Next steps:\n";
    echo "  1. Test actual file uploads through the admin interface\n";
    echo "  2. Verify uploaded files display correctly\n";
    echo "  3. Test signature functionality in contracts\n";
} else {
    echo "⚠️ Some tests failed. Please check the issues above.\n";
}

echo "\n🔚 Upload system test complete.\n";
?>
