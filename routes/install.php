<?php

declare(strict_types=1);

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::prefix('install')->name('install.')->group(function (): void {
    Route::get('/', [InstallController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/database', [InstallController::class, 'databaseForm'])->name('database');
    Route::post('/permissions', [InstallController::class, 'permissions'])->name('permissions');
    Route::post('/test-db', [InstallController::class, 'testDb'])->name('testDb');
    // Route to write DB settings to .env from the installer UI
    Route::post('/save-db', [InstallController::class, 'saveDb'])->name('saveDb');
    Route::post('/create-admin', [InstallController::class, 'createAdmin'])->name('createAdmin');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});
