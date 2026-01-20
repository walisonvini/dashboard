<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use App\Events\LogActionEvent;

class TicketService
{
    public function create(array $data, User $user): Ticket
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

    public function updateTicket(Ticket $ticket, $data): Ticket
    {
        if(!$ticket->isOpen() && !auth()->user()->hasPermissionTo('tickets.support')) {
            throw new \Exception('Ticket is not open and cannot be updated');
        }

        $this->canUserEditTicket($ticket, auth()->user());

        $oldData = $ticket->only(['title', 'description', 'category_id', 'priority', 'status']);

        $ticket->update($data);

        event(new LogActionEvent('Ticket', $ticket->id, 'updated', [
            'old' => $oldData,
            'new' => $ticket->only(['title', 'description', 'category_id', 'priority', 'status'])
        ]));

        return $ticket;
    }

    public function assignTicket(Ticket $ticket): void
    {
        if($ticket->users()->where('role', '<>', 'assigned')->where('user_id', auth()->user()->id)->exists()) {
            throw new \Exception('You cannot provide support if you are the requester or already have another role in this ticket');
        }

        $ticket->users()->attach(auth()->user()->id, ['role' => 'assigned']);

        event(new LogActionEvent('Ticket', $ticket->id, 'assigned', [
            'user_id' => auth()->user()->id
        ]));
    }

    public function unassignTicket(Ticket $ticket): void
    {
        $ticket->users()->detach(auth()->user()->id);

        event(new LogActionEvent('Ticket', $ticket->id, 'unassigned', [
            'user_id' => auth()->user()->id
        ]));
    }

    public function canAccessTicket(Ticket $ticket): bool
    {
        $user = auth()->user();
        $isUserInTicket = $ticket->users->contains($user->id);
        $hasSupportPermission = $user->hasPermissionTo('tickets.support');
        
        return $isUserInTicket || $hasSupportPermission;
    }

    public function showAvailableUsers(Ticket $ticket): Collection
    {
        $users = User::permission('tickets.view')
        ->whereDoesntHave('tickets', function ($query) use ($ticket) {
            $query->where('tickets.id', $ticket->id);
        })
        ->get(['id', 'name', 'email']);

        return $users;
    }

    public function addUser(Ticket $ticket, User $user, string $role = 'observer'): Ticket
    {
        if ($ticket->users->contains($user->id)) {
            throw new \Exception('User is already in this ticket.');
        }

        $this->canUserEditTicket($ticket, auth()->user());

        $ticket->users()->attach($user->id, ['role' => $role]);

        $ticket->load('users');

        event(new LogActionEvent('Ticket', $ticket->id, 'user_added', [
            'added_user_id' => $user->id,
            'role' => $role
        ]));

        return $ticket;
    }

    public function removeUser(Ticket $ticket, User $user): Ticket
    {
        if (!$ticket->users->contains($user->id)) {
            throw new \Exception('User is not in this ticket.');
        }

        $this->canUserEditTicket($ticket, auth()->user());

        $userRole = $ticket->users()->where('user_id', $user->id)->first()->pivot->role;
        
        if (!in_array($userRole, ['observer', 'contributor'])) {
            throw new \Exception('This user cannot be removed from the ticket.');
        }

        $ticket->users()->detach($user->id);

        $ticket->load('users');

        event(new LogActionEvent('Ticket', $ticket->id, 'user_removed', [
            'removed_user_id' => $user->id,
            'role' => $userRole
        ]));

        return $ticket;
    }

    public function canUserEditTicket(Ticket $ticket, User $user): void
    {
        if(!$ticket->users->contains($user->id)) {
            throw new \Exception('User is not in this ticket.', 403);
        }

        $userRole = $ticket->users()->where('user_id', $user->id)->first()?->pivot->role;
        
        if(in_array($userRole, ['observer'])) {
            throw new \Exception('Observers cannot modify ticket information.', 403);
        }
    }

    public function getPaginatedTicketsForUser(User $user, Request $request): LengthAwarePaginator
    {
        $query = Ticket::with(['category', 'users']);

        $this->applyUserPermissions($query, $user);
        
        $this->applyFilters($query, $request, $user);

        return $query->paginate(10);
    }

    public function getPaginatedCommentsForTicket(Ticket $ticket): LengthAwarePaginator
    {
        $query = $ticket->comments()
            ->select(['id', 'ticket_id', 'user_id', 'comment', 'created_at'])
            ->with('user:id,name,email')
            ->latest();

        return $query->paginate(20);
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
