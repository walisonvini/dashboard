<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permissions.view');
    Route::get('permissions/role/{role}', [PermissionController::class, 'getMenusWithPermissionsForRole'])->name('permissions.getMenusWithPermissionsForRole');
    Route::post('permissions/{role}', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:permissions.create');
}); 