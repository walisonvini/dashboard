<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;

use Inertia\Inertia;

use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;

use App\Services\TicketService;

class TicketController extends Controller
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index()
    {
        $tickets = Ticket::with('category')->latest()->get();
        return Inertia::render('tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        $categories = TicketCategory::all();
        return Inertia::render('tickets/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $this->ticketService->create($request->validated(), auth()->user());

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('category', 'attachments', 'comments.user', 'comments.attachments');

        return Inertia::render('tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => [
                    'id' => $ticket->category->id,
                    'name' => $ticket->category->name,
                ],
                'created_at' => $ticket->created_at,
                'initial_comment' => $ticket->initial_comment,
                'attachments' => $ticket->attachments->map(fn($file) => [
                    'name' => $file->name,
                    'url' => $file->url,
                ]),
                'comments' => $ticket->comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'body' => $comment->comment,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'attachments' => $comment->attachments?->map(fn($file) => [
                        'name' => $file->name,
                        'url' => $file->url,
                    ]) ?? [],
                ]),
            ],
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $categories = TicketCategory::all();
        return Inertia::render('tickets/Edit', [
            'ticket' => $ticket,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->all());
        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully');
    }
}
