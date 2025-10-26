<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Services\TicketService;
use App\Services\TicketAttachmentService;
use App\Http\Requests\TicketAttachment\StoreTicketAttachmentRequest;

class TicketAttachmentController extends Controller
{
    public function __construct(
        private TicketAttachmentService $attachmentService,
        private TicketService $ticketService
    ) {}

    public function store(StoreTicketAttachmentRequest $request, Ticket $ticket)
    {
        $files = $request->file('files');

        if (!$files || count($files) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No files uploaded'
            ], 400);
        }

        try {
            $attachments = $this->attachmentService->uploadFiles($ticket, $files);

            return response()->json([
                'status' => 'success',
                'attachments' => $attachments
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function download(TicketAttachment $attachment)
    {
        try {
            $filePath = $this->attachmentService->downloadFile($attachment);
            return response()->download($filePath, $attachment->original_name);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
