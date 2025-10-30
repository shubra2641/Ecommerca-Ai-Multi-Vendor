<?php

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Installation Routes
|--------------------------------------------------------------------------
|
| These routes handle the installation process for Easy Store.
| They are only accessible when the system is not installed.
|
*/

Route::prefix('install')->name('install.')->group(function () {
    // Welcome page
    Route::get('/', [InstallController::class, 'welcome'])->name('welcome');

    // Requirements check
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');

    // Database configuration
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database', [InstallController::class, 'databaseStore'])->name('database.store');

    // Admin account creation
    Route::get('/admin', [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin', [InstallController::class, 'adminStore'])->name('admin.store');

    // License verification
    Route::get('/license', [InstallController::class, 'license'])->name('license');
    Route::post('/license', [InstallController::class, 'licenseStore'])->name('license.store');

    // Installation process
    Route::get('/install', [InstallController::class, 'install'])->name('install');
    Route::post('/process', [InstallController::class, 'installProcess'])->name('process');

    // Installation complete
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});
