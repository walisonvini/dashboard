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
        TicketCategory::insert([
            [
                'name' => 'Technical Support',
                'description' => 'Technical support for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Feature Request',
                'description' => 'Feature request for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Access Request',
                'description' => 'Access request for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internal Systems',
                'description' => 'Internal systems for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General Request',
                'description' => 'General request for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'description' => 'Other',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $categories = TicketCategory::all();

        foreach ($categories as $category) {
            Ticket::factory()->create([
                'category_id' => $category->id,
            ]);
        }

        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            $ticket->users()->attach(User::all()->random()->id);
        }
    }
}