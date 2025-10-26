<?php

namespace Tests\Feature\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\TicketStatus\TicketStatus;

class TicketCommentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::factory()->create();

        $user->assignRole('employee');

        $this->actingAs($user);
    }

    public function test_user_can_send_comment_to_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $response = $this->post('/tickets/' . $ticket->id . '/comments', ['comment' => 'teste']);

        $response->assertStatus(201);

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'comment' => 'teste',
            'user_id' => auth()->user()->id,
        ]);
    }

    public function test_user_cannot_send_comment_to_ticket_if_ticket_is_closed_or_canceled(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Closed]);

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $response = $this->post('/tickets/' . $ticket->id . '/comments', ['comment' => 'teste']);

        $response->assertStatus(409);

        $response->assertJson([
            'status' => 'error',
            'message' => 'Could not send comment.',
            'error' => 'Ticket is closed or canceled.',
        ]);
    }

    public function test_observer_cannot_send_comment_to_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        $ticket->users()->attach(auth()->user()->id, ['role' => 'observer']);

        $response = $this->post('/tickets/' . $ticket->id . '/comments', ['comment' => 'teste']);

        $response->assertStatus(403);

        $response->assertJson([
            'status' => 'error',
            'message' => 'Could not send comment.',
            'error' => 'Observers cannot modify ticket information.',
        ]);
    }

    public function test_user_cannot_send_comment_to_ticket_if_user_is_not_in_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        $response = $this->post('/tickets/' . $ticket->id . '/comments', ['comment' => 'teste']);

        $response->assertStatus(403);

        $response->assertJson([
            'status' => 'error',
            'message' => 'Could not send comment.',
            'error' => 'User is not in this ticket.',
        ]);
    }
}
