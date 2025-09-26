<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function create(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $ticket = Ticket::create([
                'code' => 'TCK-' . strtoupper(Str::random(6)),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'open',
            ]);

            $ticket->users()->attach($user->id, ['role' => 'requester']);

            if (!empty($data['initial_comment'])) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => $data['initial_comment'],
                ]);
            }

            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $path = $file->store('ticket_attachments');

                    $ticket->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            return $ticket;
        });
    }

    public function updateTicket(Ticket $ticket, $data)
    {
        if(!$ticket->isOpen() && !auth()->user()->hasPermissionTo('tickets.support')) {
            throw new \Exception('Ticket is not open and cannot be updated');
        }

        $ticket->update($data);

        return $ticket;
    }

    public function assignTicket(Ticket $ticket)
    {
        if($ticket->users()->where('role', '<>', 'assigned')->where('user_id', auth()->user()->id)->exists()) {
            throw new \Exception('You cannot provide support if you are the requester or already have another role in this ticket');
        }

        $ticket->users()->attach(auth()->user()->id, ['role' => 'assigned']);
    }

    public function unassignTicket(Ticket $ticket)
    {
        $ticket->users()->detach(auth()->user()->id);
    }

    public function canAccessTicket(Ticket $ticket): bool
    {
        $user = auth()->user();
        $isUserInTicket = $ticket->users->contains($user->id);
        $hasSupportPermission = $user->hasPermissionTo('tickets.support');
        
        return $isUserInTicket || $hasSupportPermission;
    }

    public function showAvailableUsers(Ticket $ticket)
    {
        $users = User::permission('tickets.view')
        ->whereDoesntHave('tickets', function ($query) use ($ticket) {
            $query->where('tickets.id', $ticket->id);
        })
        ->get(['id', 'name', 'email']);

        return $users;
    }

    public function addUser(Ticket $ticket, User $user, string $role = 'observer')
    {
        if ($ticket->users->contains($user->id)) {
            throw new \Exception('User is already in this ticket.');
        }

        $ticket->users()->attach($user->id, ['role' => $role]);

        $ticket->load('users');

        return $ticket;
    }

    public function removeUser(Ticket $ticket, User $user)
    {
        if (!$ticket->users->contains($user->id)) {
            throw new \Exception('User is not in this ticket.');
        }

        $userRole = $ticket->users()->where('user_id', $user->id)->first()->pivot->role;
        
        if (!in_array($userRole, ['observer', 'contributor'])) {
            throw new \Exception('This user cannot be removed from the ticket.');
        }

        $ticket->users()->detach($user->id);

        $ticket->load('users');

        return $ticket;
    }

    public function canUserEditTicket(Ticket $ticket, User $user): bool
    {
        if(!$ticket->users->contains($user->id)) {
            throw new \Exception('User is not in this ticket.', 403);
        }

        $userRole = $ticket->users()->where('user_id', $user->id)->first()?->pivot->role;
        return !in_array($userRole, ['observer']);
    }

    public function getPaginatedTicketsForUser(User $user, Request $request): LengthAwarePaginator
    {
        $query = Ticket::with(['category', 'users']);

        $this->applyUserPermissions($query, $user);
        
        $this->applyFilters($query, $request, $user);

        return $query->paginate(10);
    }

    private function applyUserPermissions($query, User $user): void
    {
        if ($user->hasPermissionTo('tickets.support')) {
            $query->latest();
        } else {
            $query->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest('updated_at');
        }
    }

    private function applyFilters($query, Request $request, User $user): void
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                  ->orWhere('title', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assignment')) {
            $this->applyAssignmentFilter($query, $request->assignment, $user);
        }
    }

    private function applyAssignmentFilter($query, string $value, User $user)
    {
        if ($value === 'involved') {
            return $query->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest('updated_at');
        }

        if ($value === 'not_involved') {
            return $query->whereDoesntHave('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
