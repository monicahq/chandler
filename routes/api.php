<?php

use App\Domains\Settings\ManageUsers\Api\Controllers\UserController;
use App\Domains\Vault\ManageVault\Api\Controllers\VaultController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:read'])->get('/user', UserController::class);

Route::middleware(['auth:sanctum', 'abilities:read'])->group(function () {
    // users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

    // vaults
    Route::prefix('vaults')->group(function () {
        Route::get('', [VaultController::class, 'index'])->name('vaults.index');
        Route::post('', [VaultController::class, 'store'])->name('vaults.store');

        Route::prefix('{vault}')->group(function () {
            Route::get('', [VaultController::class, 'show'])->name('vaults.show');
            Route::put('', [VaultController::class, 'update'])->name('vaults.update');
            Route::delete('', [VaultController::class, 'destroy'])->name('vaults.destroy');
        });
    });
});
