<?php

echo "=== Signature System Test ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check signature directory
echo "1. Testing Signature Directory:\n";
echo str_repeat("-", 40) . "\n";

$sigDir = 'public/uploads/contracts/signatures';
if (is_dir($sigDir)) {
    echo "✓ Signature directory exists\n";
    
    if (is_writable($sigDir)) {
        echo "✓ Signature directory is writable\n";
        
        // Test creating a signature file
        $testSig = $sigDir . '/test_signature_' . time() . '.png';
        $testData = 'Test signature data';
        
        if (file_put_contents($testSig, $testData)) {
            echo "✓ Can create signature files\n";
            
            // Test reading the file
            if (file_get_contents($testSig) === $testData) {
                echo "✓ Can read signature files\n";
            } else {
                echo "✗ Cannot read signature files\n";
            }
            
            // Clean up
            unlink($testSig);
            echo "✓ Test file cleaned up\n";
        } else {
            echo "✗ Cannot create signature files\n";
        }
    } else {
        echo "✗ Signature directory is not writable\n";
    }
} else {
    echo "✗ Signature directory does not exist\n";
}

echo "\n2. Testing PDF Directory:\n";
echo str_repeat("-", 40) . "\n";

$pdfDir = 'public/uploads/contracts/pdfs';
if (is_dir($pdfDir)) {
    echo "✓ PDF directory exists\n";
    
    if (is_writable($pdfDir)) {
        echo "✓ PDF directory is writable\n";
        
        // Test creating a PDF file
        $testPdf = $pdfDir . '/test_contract_' . time() . '.pdf';
        $testData = 'Test PDF data';
        
        if (file_put_contents($testPdf, $testData)) {
            echo "✓ Can create PDF files\n";
            unlink($testPdf);
        } else {
            echo "✗ Cannot create PDF files\n";
        }
    } else {
        echo "✗ PDF directory is not writable\n";
    }
} else {
    echo "✗ PDF directory does not exist\n";
}

echo "\n3. Testing Contract Model:\n";
echo str_repeat("-", 40) . "\n";

if (file_exists('app/Models/Contract1.php')) {
    echo "✓ Contract1 model exists\n";
    
    // Check for signature handling
    $contractCode = file_get_contents('app/Models/Contract1.php');
    if (strpos($contractCode, 'signature') !== false) {
        echo "✓ Contract model has signature handling\n";
    } else {
        echo "? Contract model signature handling unclear\n";
    }
} else {
    echo "✗ Contract1 model not found\n";
}

echo "\n4. Testing Filament Resource:\n";
echo str_repeat("-", 40) . "\n";

if (file_exists('app/Filament/Resources/Contract1Resource.php')) {
    echo "✓ Contract1Resource exists\n";
    
    // Check for signature field
    $resourceCode = file_get_contents('app/Filament/Resources/Contract1Resource.php');
    if (strpos($resourceCode, 'signature') !== false) {
        echo "✓ Resource has signature field\n";
    } else {
        echo "? Resource signature field unclear\n";
    }
    
    if (strpos($resourceCode, "disk('uploads')") !== false) {
        echo "✓ Resource uses uploads disk\n";
    } else {
        echo "? Resource disk configuration unclear\n";
    }
} else {
    echo "✗ Contract1Resource not found\n";
}

echo "\n=== SIGNATURE TEST SUMMARY ===\n";
echo "✓ = Test passed\n";
echo "✗ = Test failed\n";
echo "? = Needs manual verification\n";

echo "\nIf all tests show ✓, your signature system is ready!\n";
echo "Next step: Test actual signature upload in the admin interface.\n";

?>
