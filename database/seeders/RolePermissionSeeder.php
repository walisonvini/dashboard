<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionGroups = [
            'home' => ['home.view'],
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
            'permissions' => ['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'],
            'tickets' => ['tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete', 'tickets.support'],
            'ticket-categories' => ['ticket-categories.view', 'ticket-categories.create', 'ticket-categories.edit', 'ticket-categories.delete'],
        ];

        $super = Role::where('name', 'super')->first();
        $super->givePermissionTo(Permission::all());

        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo($permissionGroups['home'], $permissionGroups['users'], $permissionGroups['tickets']);

        $employee = Role::where('name', 'employee')->first();
        $employeeTickets = ['tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete'];
        $employee->givePermissionTo($permissionGroups['home'], $employeeTickets);

        $support = Role::where('name', 'support')->first();
        $support->givePermissionTo($permissionGroups['home'], $permissionGroups['tickets']);

        $default = Role::where('name', 'default')->first();
        $default->givePermissionTo($permissionGroups['home']);
    }
}
