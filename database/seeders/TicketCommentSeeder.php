<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketComment;

class TicketCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $users = User::all();

        foreach ($tickets as $ticket) {
            TicketComment::factory()->count(10)->create([
                'ticket_id' => $ticket->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
