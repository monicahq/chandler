<?php

use App\Domains\Settings\ManageUsers\Api\Controllers\UserController;
use App\Domains\Vault\ManageVault\Api\Controllers\VaultController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // users
    Route::middleware('abilities:read')->group(function () {
        Route::get('user', [UserController::class, 'user']);
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // vaults
    Route::prefix('vaults')->group(function () {
        Route::get('', [VaultController::class, 'index'])->name('vaults.index')->middleware('abilities:read');
        Route::post('', [VaultController::class, 'store'])->name('vaults.store')->middleware('abilities:update');

        Route::prefix('{vault}')->group(function () {
            Route::get('', [VaultController::class, 'show'])->name('vaults.show');
            Route::put('', [VaultController::class, 'update'])->name('vaults.update');
            Route::delete('', [VaultController::class, 'destroy'])->name('vaults.destroy');
        });
    });
});
