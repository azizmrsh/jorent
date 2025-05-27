<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

try {
    echo "Testing view existence...\n";
    
    // Test view existence
    if (view()->exists('filament.widgets.collapsible-widget-group-simple')) {
        echo "✓ View exists: filament.widgets.collapsible-widget-group-simple\n";
    } else {
        echo "✗ View not found: filament.widgets.collapsible-widget-group-simple\n";
    }
    
    // Test the widget class
    $widget = new App\Filament\Widgets\CollapsibleWidgetGroup();
    echo "✓ Widget instantiated successfully\n";
    
    // Test render method
    $widget->title = 'Test Title';
    $widget->widgets = ['Test Widget'];
    
    $rendered = $widget->render();
    echo "✓ Widget rendered successfully\n";
    echo "Rendered content length: " . strlen($rendered) . " characters\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
