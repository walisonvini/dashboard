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
            $this->ticketService->canUserEditTicket($ticket, auth()->user());

            if($ticket->isClosedOrCanceled()) {
                throw new \Exception('Ticket is closed or canceled.', 409);
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
            ], $e->getCode());
        }
    }

    public function show(Ticket $ticket): JsonResponse
    {
        try {
            if (!$this->ticketService->canAccessTicket($ticket)) {
                throw new \Exception('User cannot access this ticket.', 403);
            }

            $comments = $this->ticketService->getPaginatedCommentsForTicket($ticket, request());

            return response()->json($comments, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not retrieve comments.',
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
