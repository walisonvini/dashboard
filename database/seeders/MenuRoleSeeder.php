<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Menu;
use Spatie\Permission\Models\Role;

class MenuRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = Menu::all();
        $roles = Role::all();

        $menus->each(function ($menu) use ($roles) {
            $menu->roles()->attach($roles);
        });
    }
}
