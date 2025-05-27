<?php

echo "Testing CollapsibleWidgetGroup instantiation...\n";

try {
    $widget = new App\Filament\Widgets\CollapsibleWidgetGroup();
    echo "✅ SUCCESS: Widget created without constructor arguments!\n";
    echo "✅ Title: '{$widget->title}'\n";
    echo "✅ Collapsible: " . ($widget->collapsible ? 'true' : 'false') . "\n";
    echo "✅ Collapsed: " . ($widget->collapsed ? 'true' : 'false') . "\n";
    echo "✅ Icon: '{$widget->icon}'\n";
    echo "✅ Widgets count: " . count($widget->widgets) . "\n";
    
    echo "\nTesting static factory method...\n";
    $configuredWidget = App\Filament\Widgets\CollapsibleWidgetGroup::create(
        'Test Group',
        ['widget1', 'widget2'],
        true,
        false,
        'heroicon-o-chart-bar'
    );
    echo "✅ SUCCESS: Static factory method works!\n";
    echo "✅ Configured title: '{$configuredWidget->title}'\n";
    echo "✅ Configured widgets count: " . count($configuredWidget->widgets) . "\n";
    echo "✅ Configured icon: '{$configuredWidget->icon}'\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "❌ File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🎉 Test completed!\n";
