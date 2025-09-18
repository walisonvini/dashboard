<?php

namespace Tests\Feature\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketCategory;
class TicketActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::factory()->create();

        $user->assignRole('super');

        $this->actingAs($user);
    }

    public function test_user_can_update_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $category = TicketCategory::factory()->create();

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $response = $this->put('/tickets/' . $ticket->id, [
            'status' => 'closed',
            'priority' => 'low',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();

        $response->assertSessionHas('success', 'Ticket updated successfully');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
            'priority' => 'low',
            'category_id' => $category->id,
        ]);
    }

    public function test_assign_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->post('/tickets/' . $ticket->id . '/assign');

        $response->assertRedirect();

        $response->assertSessionHas('success', 'Ticket assigned successfully');
    }

    public function test_unassign_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $ticket->users()->attach(auth()->user()->id, ['role' => 'assigned']);

        $response = $this->post('/tickets/' . $ticket->id . '/unassign');

        $response->assertRedirect();

        $response->assertSessionHas('success', 'Ticket unassigned successfully');

        $this->assertFalse($ticket->users->contains(auth()->user()->id));
    }

    public function test_user_cannot_assign_ticket_if_they_are_the_requester(): void
    {
        $ticket = Ticket::factory()->create();

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $response = $this->post('/tickets/' . $ticket->id . '/assign');

        $response->assertRedirect();

        $response->assertSessionHas('error', 'You cannot provide support if you are the requester or already have another role in this ticket');
    }

    public function test_add_user_to_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $user = User::factory()->create();
        $user->assignRole('employee');

        $response = $this->post('/tickets/' . $ticket->id . '/users/' . $user->id, [
            'role' => 'observer',
        ]);

        $response->assertRedirect();

        $response->assertSessionHas('success', 'User added to ticket successfully');

        $this->assertTrue($ticket->users->contains($user->id));
    }

    public function test_remove_user_from_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $ticket->users()->attach(auth()->user()->id, ['role' => 'requester']);

        $user = User::factory()->create();
        $user->assignRole('employee');

        $ticket->users()->attach($user->id, ['role' => 'observer']);

        $response = $this->delete('/tickets/' . $ticket->id . '/users/' . $user->id);

        $response->assertRedirect();

        $response->assertSessionHas('success', 'User removed from ticket successfully');

        $this->assertFalse($ticket->users->contains($user->id));
    }
}
