<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super = User::where('name', 'super')->first();
        $admin = User::where('name', 'admin')->first();
        $employee = User::where('name', 'employee')->first();

        $super->assignRole('super');
        $admin->assignRole('admin');
        $employee->assignRole('employee');
    }
}
