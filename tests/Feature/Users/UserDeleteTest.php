<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Role;

class UserDeleteTest extends TestCase
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
    public function test_user_can_be_deleted(): void
    {
        $user = User::factory()->create();

        Role::firstOrCreate(['name' => 'default']);

        $user->assignRole('default');

        $response = $this->delete('/users/' . $user->id);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User deleted successfully');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => now(),
        ]);
    }

    public function test_user_can_be_not_deleted(): void
    {
        $superUser = User::where('name', 'super')->first();

        if (!$superUser) {
            $superUser = User::factory()->create(['name' => 'super']);
            $superUser->assignRole('super');
        }

        $response = $this->delete('/users/' . $superUser->id);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error', 'User cannot be deleted');
    }
}
