<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Dashboard\AdminController;
use App\Http\Controllers\Web\Dashboard\InventoryManagerController;
use App\Http\Controllers\Web\Dashboard\PharmacistController;
use App\Http\Controllers\Web\Dashboard\SalespersonController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
});

Route::middleware(['auth', 'role:salesperson'])->group(function () {
    Route::get('/salesperson/dashboard', [SalespersonController::class, 'index'])
        ->name('salesperson.dashboard');
});

Route::middleware(['auth', 'role:inventory_manager'])->group(function () {
    Route::get('/inventory-manager/dashboard', [InventoryManagerController::class, 'index'])
        ->name('inventory-manager.dashboard');
});

Route::middleware(['auth', 'role:pharmacist'])->group(function () {
    Route::get('/pharmacist/dashboard', [PharmacistController::class, 'index'])
        ->name('pharmacist.dashboard');
});
