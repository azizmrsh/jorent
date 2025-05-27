<?php

// Test the CollapsibleWidgetGroup widget
require_once __DIR__ . '/vendor/autoload.php';

use App\Filament\Widgets\CollapsibleWidgetGroup;

try {
    echo "Testing CollapsibleWidgetGroup widget...\n";
    
    // Test widget instantiation
    $widget = new CollapsibleWidgetGroup();
    echo "✓ Widget instantiated successfully\n";
    
    // Test view path
    $reflection = new ReflectionClass($widget);
    $viewProperty = $reflection->getProperty('view');
    $viewProperty->setAccessible(true);
    $viewPath = $viewProperty->getValue($widget);
    echo "✓ View path: " . $viewPath . "\n";
    
    // Test static factory method
    $configuredWidget = CollapsibleWidgetGroup::create(
        'Test Widget Group',
        ['Widget 1', 'Widget 2']
    );
    echo "✓ Static factory method works\n";
    
    echo "✓ All tests passed! Widget should work now.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
