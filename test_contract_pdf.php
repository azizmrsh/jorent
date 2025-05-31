<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🧪 Testing Contract PDF Generation...\n";
    
    // Check if contracts exist
    $contractCount = \App\Models\Contract1::count();
    echo "📊 Found {$contractCount} contracts in database\n";
    
    if ($contractCount > 0) {
        // Get first contract
        $contract = \App\Models\Contract1::first();
        echo "📄 Testing with contract ID: {$contract->id}\n";
        
        // Generate PDF
        $service = new \App\Services\ContractPdfService();
        $path = $service->generateContractPdf($contract);
        
        if ($path) {
            echo "✅ PDF generated successfully at: {$path}\n";
            
            // Check if file exists
            $fullPath = storage_path("app/public/{$path}");
            if (file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                echo "📁 File exists, size: " . number_format($fileSize) . " bytes\n";
            } else {
                echo "❌ File not found at: {$fullPath}\n";
            }
        } else {
            echo "❌ PDF generation failed\n";
        }
    } else {
        echo "⚠️ No contracts found in database\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Test completed\n";
