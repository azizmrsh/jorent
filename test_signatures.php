<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Contract1;

echo "Testing signature paths in contracts...\n\n";

// Check contracts with signatures
$contractsWithTenantSignatures = Contract1::whereNotNull('tenant_signature_path')->get();
$contractsWithLandlordSignatures = Contract1::whereNotNull('landlord_signature_path')->get();

echo "Contracts with tenant signatures: " . $contractsWithTenantSignatures->count() . "\n";
echo "Contracts with landlord signatures: " . $contractsWithLandlordSignatures->count() . "\n\n";

// Show first few contracts with signatures
if ($contractsWithTenantSignatures->count() > 0) {
    echo "Sample tenant signature paths:\n";
    foreach ($contractsWithTenantSignatures->take(3) as $contract) {
        echo "Contract ID: {$contract->id}, Path: {$contract->tenant_signature_path}\n";
        
        // Check if file exists in the correct location
        $correctPath = public_path('uploads/' . $contract->tenant_signature_path);
        $oldPath = public_path('contracts/signatures/' . basename($contract->tenant_signature_path));
        
        echo "  - File exists at correct path (uploads/): " . (file_exists($correctPath) ? 'YES' : 'NO') . "\n";
        echo "  - File exists at old path (contracts/): " . (file_exists($oldPath) ? 'YES' : 'NO') . "\n";
        echo "  - Correct path: $correctPath\n";
        echo "  - Old path: $oldPath\n\n";
    }
}

if ($contractsWithLandlordSignatures->count() > 0) {
    echo "Sample landlord signature paths:\n";
    foreach ($contractsWithLandlordSignatures->take(3) as $contract) {
        echo "Contract ID: {$contract->id}, Path: {$contract->landlord_signature_path}\n";
        
        // Check if file exists in the correct location
        $correctPath = public_path('uploads/' . $contract->landlord_signature_path);
        $oldPath = public_path('contracts/signatures/' . basename($contract->landlord_signature_path));
        
        echo "  - File exists at correct path (uploads/): " . (file_exists($correctPath) ? 'YES' : 'NO') . "\n";
        echo "  - File exists at old path (contracts/): " . (file_exists($oldPath) ? 'YES' : 'NO') . "\n";
        echo "  - Correct path: $correctPath\n";
        echo "  - Old path: $oldPath\n\n";
    }
}

echo "Test completed.\n";
