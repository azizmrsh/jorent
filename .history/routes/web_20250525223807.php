<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;

// Home route
Route::get('/', function () {
    return view('home', ['title' => 'Home - Property Management Solution']);
})->name('home');

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

// Contracts routes
Route::resource('contracts', ContractController::class);

// Admin login redirect - redirect to Filament admin login
Route::get('/admin/login', function () {
    return redirect('/admin/login');
})->name('admin.login');