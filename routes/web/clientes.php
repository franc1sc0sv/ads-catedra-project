<?php

use App\Http\Controllers\Web\Dashboard\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,salesperson'])->group(function () {
    
    //Rutas de CustomerController
    Route::resource('clientes', CustomerController::class)->names('salesperson.clientes');

    
    Route::get('/historial/{customer?}', [CustomerController::class, 'show'])->name('salesperson.clientes.show');

});