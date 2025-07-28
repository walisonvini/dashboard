<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;

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

        return to_route('tickets.index')->with('success', 'Ticket created successfully');
    }

    public function edit(Ticket $ticket): Response
    {
        $categories = TicketCategory::all();

        return Inertia::render('tickets/Edit', [
            'ticket' => $ticket->load(['category', 'comments.user', 'attachments.uploader', 'users']),
            'categories' => $categories,
            "isSupport" => auth()->user()->hasPermissionTo('tickets.support'),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        try {
            $this->ticketService->updateTicket($ticket, $request->validated());
            return back()->with('success', 'Ticket updated successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function assign(Ticket $ticket): RedirectResponse
    {
        try {
            $this->ticketService->assignTicket($ticket);
            return back()->with('success', 'Ticket assigned successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unassign(Ticket $ticket): RedirectResponse
    {
        try {
            $this->ticketService->unassignTicket($ticket);
            return back()->with('success', 'Ticket unassigned successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addUser(Ticket $ticket, User $user, string $role): RedirectResponse
    {
        try {
            $this->ticketService->addUser($ticket, $user, $role);
            return back()->with('success', 'Contributor added successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
