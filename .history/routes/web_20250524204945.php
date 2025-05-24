<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;

Route::get('/', function () {
    return redirect('/admin');
});

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

Route::resource('contracts', ContractController::class);

// Add test route for creating units
Route::get('/create-test-units', function () {
    $properties = App\Models\Property::all();
    
    if ($properties->isEmpty()) {
        return 'No properties found! Please create properties first.';
    }

    $unitCount = 0;
    $results = [];
    
    foreach ($properties as $property) {
        $unitsToCreate = rand(2, 3);
        
        for ($i = 1; $i <= $unitsToCreate; $i++) {
            try {
                $unit = App\Models\Unit::create([
                    'property_id' => $property->id,
                    'name' => "Unit {$i} - {$property->name}",
                    'unit_number' => $i,
                    'unit_type' => 'apartment',
                    'area' => rand(80, 150),
                    'rental_price' => rand(500, 1000),
                    'status' => 'available',
                    'notes' => 'Test unit for property grid'
                ]);
                
                $results[] = "✓ Created: {$unit->name} - \${$unit->rental_price}";
                $unitCount++;
            } catch (\Exception $e) {
                $results[] = "✗ Failed to create unit {$i} for {$property->name}: " . $e->getMessage();
            }
        }
    }
    
    $results[] = "\nTotal units created: {$unitCount}";
    $results[] = "Total units in database: " . App\Models\Unit::count();
    
    return '<pre>' . implode("\n", $results) . '</pre>';
});
