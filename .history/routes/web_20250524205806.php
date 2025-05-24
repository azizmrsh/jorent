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

// Simple test route
Route::get('/test-units', function () {
    $unitsCount = App\Models\Unit::count();
    $propertiesCount = App\Models\Property::count();
    
    if ($propertiesCount > 0 && $unitsCount === 0) {
        $property = App\Models\Property::first();
        App\Models\Unit::create([
            'property_id' => $property->id,
            'name' => "Test Unit 1",
            'unit_number' => 1,
            'unit_type' => 'apartment',
            'area' => 100,
            'rental_price' => 500,
            'status' => 'available'
        ]);
        $unitsCount = App\Models\Unit::count();
    }
    
    return "Units: {$unitsCount}, Properties: {$propertiesCount}";
});