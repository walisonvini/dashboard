<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function create(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $ticket = Ticket::create([
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

    public function getTicketsForUser(User $user): Collection
    {
        if ($user->hasPermissionTo('tickets.support')) {
            return $this->getAllTickets();
        }

        return $this->getUserTickets($user);
    }

    public function getAllTickets(): Collection
    {
        return Ticket::with(['category', 'users'])
                    ->latest()
                    ->get();
    }

    public function getUserTickets(User $user): Collection
    {
        return Ticket::with(['category', 'users'])
                    ->whereHas('users', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->latest()
                    ->get();
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
}
