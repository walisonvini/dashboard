<?php

namespace Tests\Feature\Tickets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketCommentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $user = User::factory()->create();

        $user->assignRole('super');

        $this->actingAs($user);
    }
}
