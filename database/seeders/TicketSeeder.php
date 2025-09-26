<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketCategory;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::factory()->count(50)->create();

        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            $ticket->users()->attach(User::all()->random()->id);
        }
    }
}