<?php

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketAttachmentController;
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

    Route::post('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close')->middleware('permission:tickets.support');
    Route::post('tickets/{ticket}/open', [TicketController::class, 'open'])->name('tickets.open')->middleware('permission:tickets.support');
}); 