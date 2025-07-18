<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@kaplato.com'],
            [
                'name' => 'KaPlato Administrator',
                'email' => 'admin@kaplato.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'username' => 'admin',
                'verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create a karenderia owner for testing
        User::firstOrCreate(
            ['email' => 'owner@kaplato.com'],
            [
                'name' => 'Test Karenderia Owner',
                'email' => 'owner@kaplato.com',
                'password' => Hash::make('owner123'),
                'role' => 'karenderia_owner',
                'username' => 'testowner',
                'verified' => true,
                'email_verified_at' => now(),
                'phone_number' => '+639123456789',
                'address' => 'Test Address, City',
            ]
        );

        // Create a regular customer for testing
        User::firstOrCreate(
            ['email' => 'customer@kaplato.com'],
            [
                'name' => 'Test Customer',
                'email' => 'customer@kaplato.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'username' => 'testcustomer',
                'verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user and test accounts created successfully!');
        $this->command->info('Admin: admin@kaplato.com / admin123');
        $this->command->info('Owner: owner@kaplato.com / owner123');
        $this->command->info('Customer: customer@kaplato.com / customer123');
    }
}
