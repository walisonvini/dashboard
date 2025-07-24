<?php

namespace Tests\Feature\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Role;
use App\Models\User;
class RoleDeleteTest extends TestCase
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

    public function test_role_can_be_deleted(): void
    {
        $role = Role::factory()->create();

        $this->get('/roles');

        $response = $this->delete('/roles/' . $role->id);

        $response
            ->assertRedirect('/roles')
            ->assertSessionHas('success', 'Role deleted successfully');

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_role_can_be_not_deleted(): void
    {
        $superRole = Role::where('name', 'super')->first();
        $defaultRole = Role::where('name', 'default')->first();
    
        $this->get('/roles');

        $response = $this->delete('/roles/' . $superRole->id);

        $response->assertRedirect('/roles');
        $response->assertSessionHas('error', 'Role cannot be deleted');

        $response = $this->delete('/roles/' . $defaultRole->id);

        $response->assertRedirect('/roles');
        $response->assertSessionHas('error', 'Role cannot be deleted');
    }

    public function test_user_receives_default_role_when_last_role_is_deleted(): void
    {
        Role::firstOrCreate(['name' => 'default']);

        $users = User::factory()->count(10)->create();
    
        $role = Role::factory()->create();
    
        foreach ($users as $user) {
            $user->assignRole($role->name);
            $this->assertCount(1, $user->roles);
        }

        $this->get('/roles');
    
        $response = $this->delete('/roles/' . $role->id);
    
        $response->assertRedirect('/roles');
        $response->assertSessionHas('success', 'Role deleted successfully');
    
        foreach ($users as $user) {
            $this->assertTrue(
                $user->fresh()->hasRole('default'),
                "User {$user->id} should have default role after deletion"
            );
        }
    }

    public function test_user_does_not_receive_default_role_if_has_other_roles(): void
    {
        Role::firstOrCreate(['name' => 'default']);

        $roleToDelete = Role::factory()->create();
        $remainingRole = Role::factory()->create();
 

        $user = User::factory()->create();
        $user->assignRole($roleToDelete->name);
        $user->assignRole($remainingRole->name);

        $this->get('/roles');

        $response = $this->delete('/roles/' . $roleToDelete->id);

        $response->assertRedirect('/roles');
        $response->assertSessionHas('success', 'Role deleted successfully');

        $freshUser = $user->fresh();

        $this->assertTrue($freshUser->hasRole($remainingRole->name));
        $this->assertFalse($freshUser->hasRole('default'), 'User should not receive default role if they have other roles');
    }

}
