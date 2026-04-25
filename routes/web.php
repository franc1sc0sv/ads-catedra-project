<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Dashboard\AdminController;
use App\Http\Controllers\Web\Dashboard\InventoryManagerController;
use App\Http\Controllers\Web\Dashboard\PharmacistController;
use App\Http\Controllers\Web\Dashboard\SalespersonController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect(app(\App\Services\Auth\Contracts\AuthServiceInterface::class)->redirectPathAfterLogin(auth()->user()))
        : redirect()->route('login');
});

// Auth (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Role dashboards
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:salesperson'])->group(function () {
    Route::get('/sales/dashboard', [SalespersonController::class, 'index'])->name('sales.dashboard');
});

Route::middleware(['auth', 'role:inventory_manager'])->group(function () {
    Route::get('/inventory/dashboard', [InventoryManagerController::class, 'index'])->name('inventory.dashboard');
});

Route::middleware(['auth', 'role:pharmacist'])->group(function () {
    Route::get('/pharmacy/dashboard', [PharmacistController::class, 'index'])->name('pharmacy.dashboard');
});
