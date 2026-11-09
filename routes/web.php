<?php

use App\\Http\\Controllers\\DashboardController;
use App\\Http\\Controllers\\DeviceController;
use Illuminate\\Support\\Facades\\Route;

Route::middleware('atom.access')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{deviceId}', [DeviceController::class, 'show'])
        ->where('deviceId', '[A-Za-z0-9._-]+')
        ->name('devices.show');
});
