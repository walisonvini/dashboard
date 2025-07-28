<?php

namespace Tests\Feature\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Enums\TicketStatus\TicketStatus;

class TicketAttachmentTest extends TestCase
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

    public function test_user_can_upload_attachment_to_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        $response = $this->post("/tickets/{$ticket->id}/attachments", [
            'files' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $response->assertStatus(201);

        $attachment = $ticket->attachments()->first();
        $this->assertNotNull($attachment);

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'original_name' => 'test.jpg',
            'uploaded_by' => auth()->user()->id
        ]);

        $this->assertTrue(Storage::disk('public')->exists($attachment->file_path));
    }

    public function test_user_can_download_attachment_from_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        $response = $this->post("/tickets/{$ticket->id}/attachments", [
            'files' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $attachment = $ticket->attachments()->first();

        $response = $this->get("/tickets/attachments/{$attachment->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=test.jpg');
    }

    public function test_user_cannot_attach_file_to_closed_or_canceled_ticket(): void
    {
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Closed]);

        $response = $this->post("/tickets/{$ticket->id}/attachments", [
            'files' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Ticket is closed or canceled and cannot be updated'
        ]);

        $ticket = Ticket::factory()->create(['status' => TicketStatus::Canceled]);

        $response = $this->post("/tickets/{$ticket->id}/attachments", [
            'files' => [UploadedFile::fake()->image('test.jpg')],
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Ticket is closed or canceled and cannot be updated'
        ]);
    }
}
