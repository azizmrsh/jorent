<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Unit;
use App\Models\Property;

$properties = Property::all();

if ($properties->count() === 0) {
    echo "No properties found!\n";
    exit;
}

$unitTypes = ['apartment', 'studio', 'office', 'villa', 'house'];
$statuses = ['available', 'rented'];
$created = 0;

foreach ($properties as $property) {
    $unitsCount = rand(2, 4);
    
    for ($i = 1; $i <= $unitsCount; $i++) {
        try {
            $unit = Unit::create([
                'property_id' => $property->id,
                'name' => "Unit {$i} - {$property->name}",
                'unit_number' => $i,
                'unit_type' => $unitTypes[array_rand($unitTypes)],
                'area' => rand(60, 200),
                'rental_price' => rand(400, 1500),
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'Demo unit for property grid',
                'unit_details' => [
                    ['detail_name' => 'bedrooms', 'detail_value' => rand(1, 4)],
                    ['detail_name' => 'bathrooms', 'detail_value' => rand(1, 3)],
                    ['detail_name' => 'floor', 'detail_value' => rand(1, 10)]
                ],
                'features' => [
                    ['feature_name' => 'furnished', 'feature_value' => 'Yes']
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=800'
                ]
            ]);
            
            echo "Created: {$unit->name} - \${$unit->rental_price}\n";
            $created++;
        } catch (Exception $e) {
            echo "Error creating unit {$i} for {$property->name}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nTotal units created: {$created}\n";
echo "Total units in database: " . Unit::count() . "\n";
echo "Script completed!\n";
