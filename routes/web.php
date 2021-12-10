<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Vault\VaultController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\SettingsController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__.'/auth.php';

Route::get('invitation/{code}', 'Dashboard\\DashboardController@index')->name('invitation.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', 'Dashboard\\DashboardController@index')->name('dashboard');

    Route::prefix('settings')->group(function () {
        Route::get('', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('users', [UserController::class, 'index'])->name('settings.user.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('settings.user.show');
        Route::post('users', [UserController::class, 'store'])->name('settings.user.store');
    });

    // vaults
    Route::get('vaults', [VaultController::class, 'index'])->name('vault.index');
    Route::get('vaults/new', [VaultController::class, 'new'])->name('vault.new');
    Route::post('vaults', [VaultController::class, 'store'])->name('vault.store');

    Route::middleware(['vault'])->prefix('vaults/{vault}')->group(function () {
        Route::get('', [VaultController::class, 'show'])->name('vault.show');
    });

    Route::get('contacts', 'ContactController@index');

    Route::resource('settings/information', 'Settings\\InformationController');

    // contacts
    Route::get('vaults/{vault}/contacts/{contact}', 'HomeController@index')->name('contact.show');
});
