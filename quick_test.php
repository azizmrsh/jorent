<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking contracts...\n";

$contractCount = \App\Models\Contract1::count();
echo "Found {$contractCount} contracts\n";

if ($contractCount > 0) {
    $contract = \App\Models\Contract1::first();
    echo "Testing with contract ID: {$contract->id}\n";
    
    // Quick test of the service
    try {
        $service = new \App\Services\ContractPdfService();
        echo "Service instantiated successfully\n";
        
        $result = $service->generateContractPdf($contract);
        
        if ($result) {
            echo "PDF generated: {$result}\n";
            
            $fullPath = public_path($result);
            if (file_exists($fullPath)) {
                echo "File exists: " . filesize($fullPath) . " bytes\n";
            } else {
                echo "File NOT found at: {$fullPath}\n";
            }
        } else {
            echo "PDF generation returned null\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    }
}
