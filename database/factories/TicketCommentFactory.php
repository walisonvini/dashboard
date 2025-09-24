<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketComment>
 */
class TicketCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'ticket_id' => Ticket::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'comment' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(), 
        ];
    }
}
