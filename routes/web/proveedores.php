<?php

declare(strict_types=1);

use App\Http\Controllers\Web\InventoryManager\PedidoController;
use App\Http\Controllers\Web\InventoryManager\ProveedorController;
use Illuminate\Support\Facades\Route;

// Read-only: both administrator and inventory_manager.
Route::middleware(['auth', 'role:inventory_manager,administrator'])
    ->prefix('inventory-manager')
    ->name('inventory-manager.')
    ->group(function (): void {
        Route::get('proveedores', [ProveedorController::class, 'index'])
            ->name('proveedores.index');

        Route::get('pedidos', [PedidoController::class, 'index'])
            ->name('pedidos.index');
        Route::get('pedidos/create', [PedidoController::class, 'create'])
            ->name('pedidos.create');
        Route::get('pedidos/{pedido}', [PedidoController::class, 'show'])
            ->name('pedidos.show');
    });

// Write: only inventory_manager.
Route::middleware(['auth', 'role:inventory_manager'])
    ->prefix('inventory-manager')
    ->name('inventory-manager.')
    ->group(function (): void {

        // Catálogo de proveedores
        Route::get('proveedores/create', [ProveedorController::class, 'create'])
            ->name('proveedores.create');
        Route::post('proveedores', [ProveedorController::class, 'store'])
            ->name('proveedores.store');
        Route::get('proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])
            ->name('proveedores.edit');
        Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])
            ->name('proveedores.update');
        Route::patch('proveedores/{proveedor}/toggle', [ProveedorController::class, 'toggle'])
            ->name('proveedores.toggle');
        Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])
            ->name('proveedores.destroy');

        // Pedidos a proveedores — write operations only (GET create is in the read group above)
        Route::post('pedidos', [PedidoController::class, 'store'])
            ->name('pedidos.store');
        Route::patch('pedidos/{pedido}/send', [PedidoController::class, 'send'])
            ->name('pedidos.send');
        Route::patch('pedidos/{pedido}/cancel', [PedidoController::class, 'cancel'])
            ->name('pedidos.cancel');
        Route::get('pedidos/{pedido}/recibir', [PedidoController::class, 'recibirForm'])
            ->name('pedidos.recibir.form');
        Route::post('pedidos/{pedido}/recibir', [PedidoController::class, 'recibir'])
            ->name('pedidos.recibir');
    });
