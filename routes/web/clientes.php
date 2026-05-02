<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Clientes\CustomerSearchController;
use App\Http\Controllers\Web\Dashboard\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrator,salesperson'])->group(function () {

    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/buscar', [CustomerSearchController::class, 'search'])->name('buscar');
        Route::post('/quick-create', [CustomerSearchController::class, 'quickCreate'])->name('quick-create');
    });

    Route::resource('clientes', CustomerController::class)
        ->except(['destroy'])
        ->names('salesperson.clientes');

    Route::get('/historial/{customer?}', [CustomerController::class, 'show'])
        ->name('salesperson.clientes.historial');

    Route::patch('/clientes/{cliente}/frecuente', [CustomerController::class, 'toggleFrecuente'])
        ->name('salesperson.clientes.frecuente');

});

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::delete('/clientes/{cliente}', [CustomerController::class, 'destroy'])
        ->name('salesperson.clientes.destroy');

    Route::patch('/clientes/{cliente}/reactivar', [CustomerController::class, 'reactivate'])
        ->name('salesperson.clientes.reactivar');
});
