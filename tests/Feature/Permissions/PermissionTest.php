<?php

namespace Tests\Feature\Permissions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionTest extends TestCase
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

    public function test_permissions_index_page_can_be_rendered(): void
    {
        $response = $this->get('/permissions');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('roleMenus')
            ->has('roles')
        );
    }

    public function test_permissions_can_be_updated_for_role(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => 'web']);

        $this->get('/permissions');

        $response = $this->post('/permissions/' . $role->id, [
            'permissions' => ['users.view', 'users.create']
        ]);

        $response->assertRedirect('/permissions');
        $response->assertSessionHas('success', 'Permissions updated successfully');

        $this->assertTrue($role->hasPermissionTo('users.view', 'web'));
        $this->assertTrue($role->hasPermissionTo('users.create', 'web'));
    }

    public function test_permissions_can_be_removed_from_role(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => 'web']);
        
        $role->givePermissionTo(['users.view', 'users.create']);

        $this->get('/permissions');

        $response = $this->post('/permissions/' . $role->id, [
            'permissions' => []
        ]);

        $response->assertRedirect('/permissions');
        $response->assertSessionHas('success', 'Permissions updated successfully');

        $this->assertFalse($role->hasPermissionTo('users.view', 'web'));
        $this->assertFalse($role->hasPermissionTo('users.create', 'web'));
    }

    public function test_get_menus_with_permissions_for_role(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => 'web']);
        
        $role->givePermissionTo(['users.view', 'users.create']);

        $this->get('/permissions');

        $response = $this->get('/permissions/role/' . $role->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'permissions' => [
                    '*' => [
                        'name',
                        'checked'
                    ]
                ],
                'children' => [
                    '*' => [
                        'id',
                        'name',
                        'permissions' => [
                            '*' => [
                                'name',
                                'checked'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function test_permissions_update_clears_cache(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);

        $this->mock('cache', function ($mock) {
            $mock->shouldReceive('forget')
                ->with('spatie.permission.cache')
                ->once();
            $mock->shouldReceive('remember')->andReturn(null);
            $mock->shouldReceive('get')->andReturn(null);
            $mock->shouldReceive('put')->andReturn(true);
        });

        $this->get('/permissions');

        $response = $this->post('/permissions/' . $role->id, [
            'permissions' => ['users.view']
        ]);

        $response->assertRedirect('/permissions');
    }
} 