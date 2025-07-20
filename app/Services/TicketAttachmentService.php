<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAttachment;

class TicketAttachmentService
{
    public function uploadFiles(Ticket $ticket, array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store('tickets/' . $ticket->id, 'public');

            $attachment = $ticket->attachments()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'uploaded_by' => auth()->id()
            ]);

            $attachments[] = $attachment;
        }

        return $attachments;
    }

    public function downloadFile(TicketAttachment $attachment): string
    {
        $filePath = storage_path('app/public/' . $attachment->file_path);
        
        if (!file_exists($filePath)) {
            throw new \Exception('File not found');
        }

        return $filePath;
    }
}
