<?php

use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\SessionController;
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
    Route::get('/access-logs', [AccessLogController::class, 'index'])->name('access-logs.index');
    Route::get('/access-logs/{logId}', [AccessLogController::class, 'show'])
        ->whereNumber('logId')
        ->name('access-logs.show');
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/{sessionId}', [SessionController::class, 'show'])
        ->where('sessionId', '[A-Za-z0-9._-]+')
        ->name('sessions.show');
});
