<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index')->middleware('permission:tickets.view');
    Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create')->middleware('permission:tickets.create');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store')->middleware('permission:tickets.create');
    Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit')->middleware('permission:tickets.edit');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update')->middleware('permission:tickets.edit');

    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store')->middleware('permission:tickets.view');
    Route::post('tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])->name('tickets.attachments.store')->middleware('permission:tickets.view');
    Route::get('tickets/attachments/{attachment}/download', [TicketAttachmentController::class, 'download'])->name('tickets.attachments.download')->middleware('permission:tickets.view');

    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign')->middleware('permission:tickets.support');
    Route::post('tickets/{ticket}/unassign', [TicketController::class, 'unassign'])->name('tickets.unassign')->middleware('permission:tickets.support');

    Route::get('tickets/{ticket}/available-users', [TicketController::class, 'showAvailableUsers'])->name('tickets.available-users')->middleware('permission:tickets.view');
    Route::post('tickets/{ticket}/users/{user}', [TicketController::class, 'addUser'])->name('tickets.users.store')->middleware('permission:tickets.view');
    Route::delete('tickets/{ticket}/users/{user}', [TicketController::class, 'removeUser'])->name('tickets.users.destroy')->middleware('permission:tickets.view');

    Route::get('ticket-categories', [TicketCategoryController::class, 'index'])->name('ticket-categories.index')->middleware('permission:ticket-categories.view');
    Route::post('ticket-categories', [TicketCategoryController::class, 'store'])->name('ticket-categories.store')->middleware('permission:ticket-categories.create');
    Route::put('ticket-categories/{category}', [TicketCategoryController::class, 'update'])->name('ticket-categories.update')->middleware('permission:ticket-categories.edit');
    Route::put('ticket-categories/{category}/deactivate', [TicketCategoryController::class, 'deactivate'])->name('ticket-categories.deactivate')->middleware('permission:ticket-categories.edit');
    Route::put('ticket-categories/{category}/reactivate', [TicketCategoryController::class, 'reactivate'])->name('ticket-categories.reactivate')->middleware('permission:ticket-categories.edit');
    Route::delete('ticket-categories/{category}', [TicketCategoryController::class, 'destroy'])->name('ticket-categories.destroy')->middleware('permission:ticket-categories.edit');
    Route::get('ticket-categories/deactivated', [TicketCategoryController::class, 'deactivated'])->name('ticket-categories.deactivated')->middleware('permission:ticket-categories.view');
}); 