<?php

namespace Tests\Feature\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;

use Illuminate\Http\UploadedFile;

use App\Models\TicketCategory;
use Illuminate\Support\Facades\Storage;

class TicketStoreTest extends TestCase
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

    public function test_user_can_create_ticket(): void
    {
        $category = TicketCategory::factory()->create();

        $response = $this->post('/tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'priority' => 'low',
            'initial_comment' => 'Test Initial Comment',
            'attachments' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $response->assertRedirect(route('tickets.index'));

        $response->assertSessionHas('success', 'Ticket created successfully');

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket'
        ]);
    }
}