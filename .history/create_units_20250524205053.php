<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Unit;
use App\Models\Property;

// Check existing data
echo "Current Units count: " . Unit::count() . "\n";
echo "Current Properties count: " . Property::count() . "\n";

// Get all properties
$properties = Property::all();

if ($properties->isEmpty()) {
    echo "No properties found! Please create properties first.\n";
    exit;
}

// Create units for each property
foreach ($properties as $property) {
    echo "Creating units for property: {$property->name}\n";
    
    // Create 2-3 units per property
    $unitsCount = rand(2, 3);
    
    for ($i = 1; $i <= $unitsCount; $i++) {
        $unitTypes = ['apartment', 'studio', 'office', 'shop', 'villa'];
        $unitType = $unitTypes[array_rand($unitTypes)];
        $area = rand(60, 250);
        $bedrooms = rand(1, 4);
        $bathrooms = rand(1, 3);
        $floor = rand(1, 8);
        $rentalPrice = rand(300, 1200);
        
        $unit = Unit::create([
            'property_id' => $property->id,
            'name' => "Unit {$i} - {$property->name}",
            'unit_number' => $i,
            'unit_type' => $unitType,
            'area' => $area,
            'unit_details' => [
                ['detail_name' => 'bedrooms', 'detail_value' => $bedrooms],
                ['detail_name' => 'bathrooms', 'detail_value' => $bathrooms],
                ['detail_name' => 'floor', 'detail_value' => $floor],
                ['detail_name' => 'Area', 'detail_value' => $area],
                ['detail_name' => 'kitchen', 'detail_value' => 1]
            ],
            'features' => [
                ['feature_name' => 'furnished', 'feature_value' => rand(0, 1) ? 'Yes' : 'No'],
                ['feature_name' => 'elevator', 'feature_value' => rand(0, 1) ? 'Yes' : 'No'],
                ['feature_name' => 'camera_security', 'feature_value' => rand(0, 1) ? 'Yes' : 'No']
            ],
            'status' => 'available',
            'rental_price' => $rentalPrice,
            'notes' => "Beautiful {$unitType} with {$bedrooms} bedrooms and {$bathrooms} bathrooms.",
            'images' => [
                'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=800',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800'
            ]
        ]);
        
        echo "Created unit: {$unit->name} - \${$rentalPrice}\n";
    }
}

echo "\nFinal Units count: " . Unit::count() . "\n";
echo "Script completed successfully!\n";
