<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'name' => 'Home',
            'icon' => 'Home',
            'route' => 'home',
            'parent_id' => null,
        ]);

        $users = Menu::create([
            'name' => 'User Management',
            'icon' => 'Users',
            'route' => null,
            'parent_id' => null,
        ]);

        $users->children()->create([
            'name' => 'Users',
            'icon' => null,
            'route' => 'users',
            'parent_id' => $users->id,
        ]);

        $users->children()->create([
            'name' => 'Roles',
            'icon' => null,
            'route' => 'roles',
            'parent_id' => $users->id,
        ]);

        $users->children()->create([
            'name' => 'Permissions',
            'icon' => null,
            'route' => 'permissions',
            'parent_id' => $users->id,
        ]);

        $serviceDesk = Menu::create([
            'name' => 'Service Desk',
            'icon' => 'LifeBuoy',
            'route' => null,
            'parent_id' => null,
        ]);

        $serviceDesk->children()->create([
            'name' => 'Tickets',
            'icon' => null,
            'route' => 'tickets',
            'parent_id' => $serviceDesk->id,
        ]);

        $serviceDesk->children()->create([
            'name' => 'Categories',
            'icon' => null,
            'route' => 'ticket-categories',
            'parent_id' => $serviceDesk->id,
        ]);

        Menu::create([
            'name' => 'Logs',
            'icon' => 'ClipboardList',
            'route' => 'logs',
            'parent_id' => null,
        ]);
    }   
}
