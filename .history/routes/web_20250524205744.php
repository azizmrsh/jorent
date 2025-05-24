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

// Temporary test route for Units
Route::get('/test-units', function () {
    try {
        $unitsCount = App\Models\Unit::count();
        $propertiesCount = App\Models\Property::count();
        
        $output = "Current Units: {$unitsCount}\n";
        $output .= "Current Properties: {$propertiesCount}\n\n";
        
        if ($propertiesCount > 0 && $unitsCount === 0) {
            // Create some test units
            $properties = App\Models\Property::take(3)->get();
            $created = 0;
            
            foreach ($properties as $property) {
                for ($i = 1; $i <= 2; $i++) {
                    App\Models\Unit::create([
                        'property_id' => $property->id,
                        'name' => "Test Unit {$i} - {$property->name}",
                        'unit_number' => $i,
                        'unit_type' => 'apartment',
                        'area' => 100,
                        'rental_price' => 500 + ($i * 100),
                        'status' => 'available',
                        'notes' => 'Test unit'
                    ]);
                    $created++;
                }
            }
            
            $output .= "Created {$created} units\n";
            $output .= "New Units count: " . App\Models\Unit::count() . "\n";
        }
        
        return '<pre>' . $output . '</pre>';
    } catch (\Exception $e) {
        return '<pre>Error: ' . $e->getMessage() . '</pre>';
    }
});