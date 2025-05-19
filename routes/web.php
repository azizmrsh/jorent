<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::resource('contracts', ContractController::class);
