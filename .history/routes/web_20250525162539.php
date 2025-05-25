<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;

Route::get('/', function () {
    return view('home', ['title' => 'jhome - Property Management Solution']);
})->name('home');

Route::get('/admin', function () {
    return redirect('/admin');
});

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

Route::resource('contracts', ContractController::class);