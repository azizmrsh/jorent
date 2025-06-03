<?php

// Test script to verify logout route exists
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test if logout route exists
try {
    $routes = \Route::getRoutes();
    $logoutRoute = $routes->getByName('logout');
    
    if ($logoutRoute) {
        echo "✅ SUCCESS: Logout route is properly defined\n";
        echo "   Method: " . implode('|', $logoutRoute->methods()) . "\n";
        echo "   URI: " . $logoutRoute->uri() . "\n";
        echo "   Name: " . $logoutRoute->getName() . "\n";
        echo "   Middleware: " . implode(', ', $logoutRoute->gatherMiddleware()) . "\n";
    } else {
        echo "❌ ERROR: Logout route not found\n";
    }
    
    // Test route generation
    $logoutUrl = route('logout');
    echo "✅ Route URL generation works: $logoutUrl\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
