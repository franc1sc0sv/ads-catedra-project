<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\CustomerController; // Asegúrate de tener este controlador
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:salesperson'])->prefix('salesperson')->group(function () {
    Route::get('/historial/{customer?}', [CustomerController::class, 'show'])
        ->name('salesperson.clientes.show');
});