<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Configuracion\ConfiguracionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrator'])
    ->prefix('admin/configuracion')
    ->name('admin.configuracion.')
    ->group(function () {
        Route::get('/', [ConfiguracionController::class, 'index'])->name('index');
        Route::patch('/{key}', [ConfiguracionController::class, 'update'])
            ->where('key', '[a-z_][a-z0-9_]*')
            ->name('update');
    });
