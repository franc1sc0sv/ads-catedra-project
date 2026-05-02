<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\SalesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:salesperson'])
    ->prefix('salesperson')
    ->name('salesperson.')
    ->group(function () {
        Route::get('/ventas/nueva', [SalesController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [SalesController::class, 'store'])->name('ventas.store');
        Route::patch('/ventas/{sale}/anular', [SalesController::class, 'cancel'])->name('ventas.cancel');
    });

// Read-only sale detail: accessible to admin, inventory manager and salesperson.
Route::middleware(['auth', 'role:administrator,inventory_manager,salesperson'])
    ->group(function () {
        Route::get('/ventas/{sale}', [SalesController::class, 'show'])->name('ventas.show');
    });
