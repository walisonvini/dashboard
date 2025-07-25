<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketComment;

use App\Enums\TicketStatus\TicketStatus;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function create(array $data, $user)
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

    public function getTicketsForUser($user): Collection
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

    public function getUserTickets($user): Collection
    {
        return Ticket::with(['category', 'users'])
                    ->whereHas('users', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->latest()
                    ->get();
    }

    public function getTicketWithDetails($ticketId)
    {
        return Ticket::with(['category', 'attachments', 'comments.user', 'comments.attachments'])
                    ->findOrFail($ticketId);
    }

    public function changeTicketStatus($ticket, $status)
    {
        $ticket->status = $status;
        $ticket->save();
    }

    public function updateTicket($ticket, $data)
    {
        if($this->verifyIfTicketIsClosed($ticket)) {
            throw new \Exception('Ticket is closed and cannot be updated');
        }

        $ticket->update($data);

        return $ticket;
    }

    public function assignTicket($ticket)
    {
        if($this->verifyIfTicketIsClosed($ticket)) {
            throw new \Exception('Ticket is closed and cannot be assigned');
        }

        $this->changeTicketStatus($ticket, TicketStatus::InProgress);

        $ticket->users()->attach(auth()->user()->id, ['role' => 'assigned']);
    }

    
    public function unassignTicket($ticket)
    {
        if($this->verifyIfTicketIsClosed($ticket)) {
            throw new \Exception('Ticket is closed and cannot be unassigned');
        }

        $ticket->users()->detach(auth()->user()->id);

        if(!$this->verifyIfTicketHaveAssignedUser($ticket)) {
            $this->changeTicketStatus($ticket, TicketStatus::Open);
        } else {
            $this->changeTicketStatus($ticket, TicketStatus::InProgress);
        }
    }

    public function verifyIfTicketHaveAssignedUser($ticket)
    {
        return $ticket->users()->where('role', 'assigned')->exists();
    }

    public function verifyIfTicketIsClosed($ticket)
    {
        return $ticket->status == TicketStatus::Closed;
    }
}
