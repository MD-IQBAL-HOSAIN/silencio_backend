<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Add admin
        $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
        if (!$admin) {
            \App\Models\User::create([
                'id' => 1,
                'name' => 'Mr. Admin',
                'email' => 'admin@admin.com',
                'role' => 'admin',
                'status' => 1,
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ]);
        } else {
            $this->command->info('Admin already exists in database');
        }


        // Add regular user
        $user = \App\Models\User::where('email', 'user@user.com')->first();
        if (!$user) {
            \App\Models\User::create([
                'id' => 2,
                'name' => 'Mr. User',
                'email' => 'user@user.com',
                'role' => 'user',
                'status' => 1,
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ]);
        } else {
            $this->command->info('User already exists in database');
        }
    }
}
