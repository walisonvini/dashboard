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
        $super = Role::where('name', 'super')->first();
        $admin = Role::where('name', 'admin')->first();
        $employee = Role::where('name', 'employee')->first();

        $super->givePermissionTo(Permission::all());
        $admin->givePermissionTo(['view users', 'create users', 'edit users', 'delete users']);
        $employee->givePermissionTo(['view users']);
    }
}
