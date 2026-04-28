<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\SalesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:salesperson'])->prefix('salesperson')->group(function () {
    Route::get('/ventas/nueva', [SalesController::class, 'create'])
        ->name('salesperson.ventas.create');
    Route::post('/ventas', [SalesController::class, 'store'])
        ->name('salesperson.ventas.store');
    Route::patch('/ventas/{sale}/anular', [SalesController::class, 'cancel'])
        ->name('salesperson.ventas.cancel');
});
