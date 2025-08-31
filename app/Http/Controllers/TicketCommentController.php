<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\TicketComments\StoreTicketCommentRequest;
use Illuminate\Http\JsonResponse;

use App\Services\TicketService;

class TicketCommentController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ){} 

    public function store(StoreTicketCommentRequest $request, Ticket $ticket): JsonResponse  
    {
        try {
            if(!$this->ticketService->canUserEditTicket($ticket, auth()->user()))
            {
                throw new \Exception('Observers cannot send comments.');
            }

            if($ticket->isClosedOrCanceled()) {
                throw new \Exception('Ticket is closed or canceled.');
            }

            $comment = $ticket->comments()->create([
                'user_id' => auth()->id(),
                'comment' => $request->validated()['comment']
            ]);
    
            return response()->json([
                'status' => 'success',
                'comment' => $comment->load('user')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not send comment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
