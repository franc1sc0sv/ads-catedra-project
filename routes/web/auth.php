<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Auth\AuthController;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect(app(AuthServiceInterface::class)->redirectPathAfterLogin(auth()->user()))
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
