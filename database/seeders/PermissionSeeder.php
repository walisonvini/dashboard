<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionGroups = [
            'home' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'tickets' => ['view', 'create', 'edit', 'delete', 'support'],
            'ticket-categories' => ['view', 'create', 'edit', 'delete'],
        ];

        foreach ($permissionGroups as $prefix => $actions) {
            foreach ($actions as $action) {
                $name = "{$prefix}.{$action}";
                Permission::firstOrCreate(['name' => $name]);
            }
        }
    }
}
