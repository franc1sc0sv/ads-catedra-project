<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\SalesController;
use Illuminate\Support\Facades\Route;

// Eliminamos el . de name() para que no se duplique si ya lo tienes en otro lado, 
// o lo definimos explícitamente:
Route::middleware(['auth', 'role:salesperson'])
    ->prefix('salesperson')
    ->name('salesperson.') // Este es el prefijo para los nombres
    ->group(function () {
        
        Route::get('/ventas/nueva', [SalesController::class, 'create'])
            ->name('ventas.create'); // Se convierte en salesperson.ventas.create
            
        Route::post('/ventas', [SalesController::class, 'store'])
            ->name('ventas.store');  // Se convierte en salesperson.ventas.store
            
        Route::patch('/ventas/{sale}/anular', [SalesController::class, 'cancel'])
            ->name('ventas.cancel'); // Se convierte en salesperson.ventas.cancel
    });