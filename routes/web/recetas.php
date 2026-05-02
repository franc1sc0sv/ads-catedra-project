<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\PharmacistController;
use Illuminate\Support\Facades\Route;

/**
 * Phase B: Recetas domain — cola de recetas e historial farmacéutico.
 * Estas rutas están protegidas para que solo el rol 'pharmacist' tenga acceso.
 */

Route::middleware(['auth', 'role:pharmacist'])->group(function () {
    
    // Vista de la cola de recetas pendientes
    Route::get('/pharmacist/cola', [PharmacistController::class, 'queue'])
        ->name('pharmacist.queue');

    // Nueva ruta para procesar la validación de la receta
    // Usamos PATCH porque estamos actualizando una parte de la receta (el estado)
    Route::patch('/pharmacist/recetas/{prescription}/validar', [PharmacistController::class, 'validate'])
        ->name('pharmacist.validate');

    // Vista del historial de recetas ya despachadas
    Route::get('/pharmacist/historial', [PharmacistController::class, 'history'])
        ->name('pharmacist.history');
});