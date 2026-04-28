<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\AdminController;
use App\Http\Controllers\Web\Dashboard\InventoryManagerController;
use App\Http\Controllers\Web\Dashboard\PharmacistController;
use App\Http\Controllers\Web\Dashboard\SalesController; 
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Dashboard de Administrador
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:administrator');

    // Dashboard de Vendedor
    Route::get('/salesperson/dashboard', [SalesController::class, 'index'])
        ->name('salesperson.dashboard')
        ->middleware('role:salesperson');

    // Dashboard de Gestor de Inventario
    Route::get('/inventory-manager/dashboard', [InventoryManagerController::class, 'index'])
        ->name('inventory-manager.dashboard')
        ->middleware('role:inventory_manager');

    // Dashboard de Farmacéutico
    Route::get('/pharmacist/dashboard', [PharmacistController::class, 'index'])
        ->name('pharmacist.dashboard')
        ->middleware('role:pharmacist');
});