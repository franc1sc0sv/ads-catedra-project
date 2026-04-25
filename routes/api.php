<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->name('api.v1.auth.')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login',    [AuthController::class, 'login'])->name('login');

    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout',  [AuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('profile',  [AuthController::class, 'profile'])->name('profile');
        Route::put('profile',  [AuthController::class, 'updateProfile'])->name('profile.update');
    });

});
