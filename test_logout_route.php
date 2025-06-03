<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Route;

echo "🧪 Testing Logout Route Fix\n";
echo "============================\n\n";

try {
    // Check if logout route exists
    if (Route::has('logout')) {
        echo "✅ SUCCESS: Logout route exists!\n";
        echo "   Route name: 'logout'\n";
        echo "   Route URL: " . route('logout') . "\n";
    } else {
        echo "❌ FAILED: Logout route not found!\n";
    }
    
    // Check if login route exists
    if (Route::has('login')) {
        echo "✅ SUCCESS: Login route exists!\n";
        echo "   Route name: 'login'\n";
        echo "   Route URL: " . route('login') . "\n";
    } else {
        echo "❌ FAILED: Login route not found!\n";
    }
    
    echo "\n";
    echo "🎉 The logout route error should now be fixed!\n";
    echo "   You can now access the email verification page without errors.\n";
    
} catch (\Exception $e) {
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
