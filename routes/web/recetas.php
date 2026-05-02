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

    // Valida todos los medicamentos de una receta por su número
    Route::patch('/pharmacist/recetas/{prescriptionNumber}/validar', [PharmacistController::class, 'validate'])
        ->name('pharmacist.validate');

    // Rechaza la receta y cancela la venta asociada
    Route::patch('/pharmacist/recetas/{prescriptionNumber}/rechazar', [PharmacistController::class, 'reject'])
        ->name('pharmacist.reject');

    // Vista del historial de recetas ya despachadas
    Route::get('/pharmacist/historial', [PharmacistController::class, 'history'])
        ->name('pharmacist.history');
});
