<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

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

    public function index(): Response
    {
        $tickets = $this->ticketService->getTicketsForUser(auth()->user());

        return Inertia::render('tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create(): Response
    {
        $categories = TicketCategory::all();
        return Inertia::render('tickets/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->ticketService->create($request->validated(), auth()->user());

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully');
    }

    public function edit(Ticket $ticket): Response
    {
        $categories = TicketCategory::all();

        return Inertia::render('tickets/Edit', [
            'ticket' => $ticket->load(['category', 'comments.user', 'attachments.uploader']),
            'categories' => $categories,
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->all());
        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully');
    }
}
