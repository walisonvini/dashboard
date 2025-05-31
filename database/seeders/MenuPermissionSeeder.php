<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $map = [
            'Home' => ['home.view'],
            'Users' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
            ],
            'Roles' => [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
            ],
            'Permissions' => [
                'permissions.view',
                'permissions.create',
                'permissions.edit',
                'permissions.delete',
            ],
        ];

        foreach ($map as $menuName => $permissions) {
            $menus = Menu::where('name', $menuName)->get();

            foreach ($menus as $menu) {
                foreach ($permissions as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();

                    if ($permission) {
                        $menu->permissions()->syncWithoutDetaching([$permission->id]);
                    }
                }
            }
        }
    }
}
