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
        User::factory()->create([
            'name' => 'super',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
        ]);
        
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'default',
            'email' => 'default@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
