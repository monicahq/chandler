<?php

use App\Domains\Settings\ManageUsers\Api\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'abilities:read'])->get('/user', UserController::class);

Route::middleware(['auth:sanctum', 'abilities:read'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
});
