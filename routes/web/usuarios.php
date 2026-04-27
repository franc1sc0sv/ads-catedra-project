<?php

declare(strict_types=1);

// Phase B: Usuarios domain — administrator-only user management.

use App\Http\Controllers\Web\Admin\PasswordController;
use App\Http\Controllers\Web\Admin\UsuarioController;
use App\Http\Controllers\Web\Auth\ConfirmPasswordController;
use Illuminate\Support\Facades\Route;

// password.confirm flow — required by `password.confirm` middleware on self-change.
Route::middleware('auth')->group(function () {
    Route::get('/password/confirm', [ConfirmPasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'store']);
});

// Self-change password (any authenticated, active user).
Route::middleware(['auth', 'active', 'password.confirm'])->group(function () {
    Route::get('/account/password', [PasswordController::class, 'editSelf'])
        ->name('account.password.edit');
    Route::put('/account/password', [PasswordController::class, 'updateSelf'])
        ->name('account.password.update');
});

// Admin-only user management.
Route::middleware(['auth', 'active', 'role:administrator'])
    ->prefix('admin/usuarios')
    ->name('admin.usuarios.')
    ->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/create', [UsuarioController::class, 'create'])->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
        Route::get('/{usuario}/password', [PasswordController::class, 'editForUser'])
            ->name('cambiarPassword');
        Route::put('/{usuario}/password', [PasswordController::class, 'resetForUser'])
            ->name('actualizarPassword');
        Route::patch('/{usuario}/activa', [UsuarioController::class, 'toggleActiva'])
            ->name('toggleActiva');
    });
