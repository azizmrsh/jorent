<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Acc;

echo "Checking for duplicate email: osaidhaj03@gmail.com\n";

try {
    $count = Acc::where('email', 'osaidhaj03@gmail.com')->count();
    echo "Found {$count} records with this email\n";
    
    if ($count > 0) {
        echo "Existing records:\n";
        $records = Acc::where('email', 'osaidhaj03@gmail.com')->get(['id', 'firstname', 'lastname', 'email']);
        foreach ($records as $record) {
            echo "- ID: {$record->id}, Name: {$record->firstname} {$record->lastname}, Email: {$record->email}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
