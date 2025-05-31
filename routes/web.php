<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::resource('users', UserController::class);

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/role/{role}', [PermissionController::class, 'getMenusWithPermissionsForRole'])->name('permissions.getMenusWithPermissionsForRole');
    Route::post('permissions/{role}', [PermissionController::class, 'store'])->name('permissions.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
