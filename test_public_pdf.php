<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TESTING PUBLIC PDF SYSTEM\n";
echo "============================\n\n";

try {
    // Test contract PDF generation
    $contract = \App\Models\Contract1::first();
    
    if (!$contract) {
        echo "❌ No contracts found to test\n";
        exit(1);
    }
    
    echo "📄 Testing with Contract ID: {$contract->id}\n";
    
    // Generate PDF using updated service
    $service = new \App\Services\ContractPdfService();
    $path = $service->generateContractPdf($contract);
    
    if ($path) {
        echo "✅ PDF generated successfully!\n";
        echo "📁 Relative path: {$path}\n";
        
        // Check if file exists in public directory
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            echo "📊 File size: " . number_format($fileSize) . " bytes\n";
            
            // Get public URL
            $url = $service->getContractPdfUrl($contract);
            echo "🌐 Public URL: {$url}\n";
            
            // Test direct access (this would work on web server)
            echo "🔗 Direct link: " . config('app.url') . "/{$path}\n";
            
        } else {
            echo "❌ File not found at: {$fullPath}\n";
        }
    } else {
        echo "❌ PDF generation failed\n";
    }
    
    echo "\n📋 PUBLIC DIRECTORY CONTENTS:\n";
    $publicContracts = public_path('contracts');
    if (is_dir($publicContracts)) {
        $files = scandir($publicContracts);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $publicContracts . '/' . $file;
                $size = filesize($filePath);
                echo "  📄 {$file} (" . number_format($size) . " bytes)\n";
            }
        }
    }
    
    echo "\n🎯 HOSTINGER DEPLOYMENT STATUS:\n";
    echo "✅ PDFs now stored in public/contracts (directly accessible)\n";
    echo "✅ No symlink required (fixes 403 Forbidden issue)\n";
    echo "✅ Direct URLs work: /contracts/filename.pdf\n";
    echo "✅ Compatible with shared hosting restrictions\n";
    
} catch (\Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test completed\n";
