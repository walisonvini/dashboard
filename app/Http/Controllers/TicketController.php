<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;

use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request): Response
    {
        $tickets = $this->ticketService->getPaginatedTicketsForUser(auth()->user(), $request);
        $categories = TicketCategory::where('is_active', true)->get();
        $authUser = auth()->user();

        return Inertia::render('tickets/Index', [
            'tickets' => $tickets->items(),
            'categories' => $categories,
            'isSupport' => $authUser->hasPermissionTo('tickets.support'),
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ]
        ]);
    }

    public function create(): Response
    {
        $categories = TicketCategory::where('is_active', true)->get();
        return Inertia::render('tickets/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->ticketService->create($request->validated(), auth()->user());

        return to_route('tickets.index')->with('success', 'Ticket created successfully');
    }

    public function edit(Ticket $ticket): Response|RedirectResponse
    {
        if (!$this->ticketService->canAccessTicket($ticket)) {
            return to_route('tickets.index')->with('error', 'You do not have permission to access this ticket.');
        }

        $categories = TicketCategory::where('is_active', true)->get();

        $authUser = auth()->user();
        $userRole = $ticket->users()->where('user_id', $authUser->id)->first()?->pivot->role ?? null;

        return Inertia::render('tickets/Edit', [
            'ticket' => $ticket->load(['category', 'comments.user', 'attachments.uploader', 'users']),
            'categories' => $categories,
            'isSupport' => $authUser->hasPermissionTo('tickets.support'),
            'authUser' => [
                'id' => $authUser->id,
                'name' => $authUser->name,
                'email' => $authUser->email,
                'role' => $userRole
            ]
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        try {
            if(!$this->ticketService->canUserEditTicket($ticket, auth()->user()))
            {
                throw new \Exception('Observers cannot modify ticket information.');
            }

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

    public function showAvailableUsers(Ticket $ticket): JsonResponse
    {
        try {
            $users = $this->ticketService->showAvailableUsers($ticket);

            return response()->json([
                'status' => 'success',
                'users' => $users,
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function addUser(Request $request, Ticket $ticket, User $user)
    {
        try {
            if(!$this->ticketService->canUserEditTicket($ticket, auth()->user()))
            {
                throw new \Exception('Observers cannot manage ticket users.');
            }

            $this->ticketService->addUser($ticket, $user, $request->role);
            return back()->with('success', 'User added to ticket successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeUser(Ticket $ticket, User $user)
    {
        try {
            if(!$this->ticketService->canUserEditTicket($ticket, auth()->user()))
            {
                throw new \Exception('Observers cannot manage ticket users.');
            }

            $this->ticketService->removeUser($ticket, $user);
            return back()->with('success', 'User removed from ticket successfully');
        } catch(\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
