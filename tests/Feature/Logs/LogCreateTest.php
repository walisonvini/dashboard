<?php

namespace Tests\Feature\Logs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\RoleService;

class LogCreateTest extends TestCase
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

    public function test_log_create()
    {
        config(['queue.default' => 'sync']);

        $role = Role::create(['name' => 'test-role']);

        $service = app(RoleService::class);
        $service->delete($role);

        $this->assertDatabaseHas('logs', [
            'model' => 'Role',
            'model_id' => $role->id,
            'action' => 'deleted',
        ]);
    }
}
