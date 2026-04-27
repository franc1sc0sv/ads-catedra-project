<?php

declare(strict_types=1);

use App\Http\Controllers\Web\InventoryManager\AjusteStockController;
use App\Http\Controllers\Web\InventoryManager\AlertasStockController;
use App\Http\Controllers\Web\InventoryManager\MedicamentoController;
use App\Http\Controllers\Web\InventoryManager\MovimientoController;
use Illuminate\Support\Facades\Route;

// Phase B: Inventario domain — catálogo, alertas, ajustes, movimientos.

// --- Lectura del catálogo: inventory_manager + salesperson + pharmacist ----
Route::middleware(['auth', 'role:inventory_manager,salesperson,pharmacist,administrator'])
    ->prefix('inventario')
    ->name('inventory-manager.catalogo.')
    ->group(function (): void {
        Route::get('/medicamentos', [MedicamentoController::class, 'index'])->name('index');
        Route::get('/medicamentos/{medicamento}', [MedicamentoController::class, 'show'])->name('show');
    });

// --- Escritura del catálogo: solo inventory_manager ----------------------
Route::middleware(['auth', 'role:inventory_manager'])
    ->prefix('inventario')
    ->name('inventory-manager.catalogo.')
    ->group(function (): void {
        Route::get('/medicamentos-nuevo', [MedicamentoController::class, 'create'])->name('create');
        Route::post('/medicamentos', [MedicamentoController::class, 'store'])->name('store');
        Route::get('/medicamentos/{medicamento}/editar', [MedicamentoController::class, 'edit'])->name('edit');
        Route::put('/medicamentos/{medicamento}', [MedicamentoController::class, 'update'])->name('update');
        Route::delete('/medicamentos/{medicamento}', [MedicamentoController::class, 'destroy'])->name('destroy');
        Route::post('/medicamentos/{medicamento}/restaurar', [MedicamentoController::class, 'restore'])->name('restore');
    });

// --- Alertas de stock: inventory_manager + administrator (read-only) ------
Route::middleware(['auth', 'role:inventory_manager,administrator'])
    ->prefix('inventario')
    ->group(function (): void {
        Route::get('/alertas-stock', [AlertasStockController::class, 'index'])
            ->name('inventory-manager.alertas.index');
    });

// --- Ajuste de stock: solo inventory_manager ------------------------------
Route::middleware(['auth', 'role:inventory_manager'])
    ->prefix('inventario')
    ->group(function (): void {
        Route::get('/ajuste-stock', [AjusteStockController::class, 'create'])
            ->name('inventory-manager.ajustes.create');
        Route::post('/ajuste-stock', [AjusteStockController::class, 'store'])
            ->name('inventory-manager.ajustes.store');
    });

// --- Historial de movimientos: inventory_manager + administrator ----------
Route::middleware(['auth', 'role:inventory_manager,administrator'])
    ->prefix('inventario')
    ->group(function (): void {
        Route::get('/movimientos', [MovimientoController::class, 'index'])
            ->name('inventory-manager.movimientos.index');
        Route::get('/medicamentos/{medicamento}/movimientos', [MovimientoController::class, 'index'])
            ->name('inventory-manager.movimientos.show');
    });
