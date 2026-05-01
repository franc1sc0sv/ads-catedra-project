<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Reportes\BitacoraController;
use App\Http\Controllers\Web\Reportes\ReporteInventarioController;
use App\Http\Controllers\Web\Reportes\ReporteMovimientosController;
use App\Http\Controllers\Web\Reportes\ReportesHubController;
use App\Http\Controllers\Web\Reportes\ReporteVentasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrator'])
    ->prefix('admin/reportes')
    ->name('admin.reportes.')
    ->group(function () {
        Route::get('/', [ReportesHubController::class, 'index'])->name('index');

        Route::get('/ventas', [ReporteVentasController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/export', [ReporteVentasController::class, 'export'])->name('ventas.export');

        Route::get('/movimientos', [ReporteMovimientosController::class, 'index'])->name('movimientos.index');
        Route::get('/movimientos/export', [ReporteMovimientosController::class, 'export'])->name('movimientos.export');

        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

Route::middleware(['auth', 'role:administrator,inventory_manager'])
    ->prefix('reportes')
    ->name('reportes.')
    ->group(function () {
        Route::get('/inventario', [ReporteInventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/export', [ReporteInventarioController::class, 'export'])->name('inventario.export');
    });
