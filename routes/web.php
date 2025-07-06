<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('home', function () {
        return Inertia::render('Home');
    })->name('home');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/users.php';
require __DIR__.'/roles.php';
require __DIR__.'/permissions.php';
