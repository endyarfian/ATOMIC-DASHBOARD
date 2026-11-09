<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OperatorController;
use Illuminate\Support\Facades\Route;

Route::middleware('atom.access')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{deviceId}', [DeviceController::class, 'show'])
        ->where('deviceId', '[A-Za-z0-9._-]+')
        ->name('devices.show');
    Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/{operatorId}', [OperatorController::class, 'show'])
        ->where('operatorId', '[A-Za-z0-9._-]+')
        ->name('operators.show');
});
