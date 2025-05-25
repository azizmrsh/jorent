<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;

Route::get('/', function () {
    return view('home', ['title' => 'Home - Property Management Solution']);
})->name('home');

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

// Contracts routes
Route::resource('contracts', ContractController::class);

// Tenants routes
Route::get('/tenants', function () {
    return view('tenants', ['title' => 'Tenants', 'subTitle' => 'Manage Tenants']);
})->name('tenants');

// Admin routes
Route::get('/admin/login', function () {
    return redirect('/admin');
})->name('admin.login');