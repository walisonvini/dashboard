<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('logs', [LogController::class, 'index'])->name('logs.index')->middleware('permission:logs.view');
    Route::get('logs/{log}', [LogController::class, 'show'])->name('logs.show')->middleware('permission:logs.show');
});