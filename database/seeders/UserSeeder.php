<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'super', 'email' => 'super@example.com'],
            ['name' => 'admin', 'email' => 'admin@example.com'],
            ['name' => 'employee', 'email' => 'employee@example.com'],
            ['name' => 'default', 'email' => 'default@example.com'],
            ['name' => 'support', 'email' => 'support@example.com'],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
            ]);
        }
    }
}
