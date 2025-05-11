<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('users', UserController::class);

    Route::get('home', function () {
        return Inertia::render('Home');
    })->name('home');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
