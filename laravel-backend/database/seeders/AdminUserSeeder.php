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

        // Create multiple karenderia owners for testing different scenarios
        User::firstOrCreate(
            ['email' => 'owner1@kaplato.com'],
            [
                'name' => 'Maria Santos',
                'email' => 'owner1@kaplato.com',
                'password' => Hash::make('owner123'),
                'role' => 'karenderia_owner',
                'username' => 'mariasantos',
                'verified' => true,
                'email_verified_at' => now(),
                'phone_number' => '+639123456789',
                'address' => '123 Rizal Street, Makati City',
            ]
        );

        User::firstOrCreate(
            ['email' => 'owner2@kaplato.com'],
            [
                'name' => 'Linda Cruz',
                'email' => 'owner2@kaplato.com',
                'password' => Hash::make('owner123'),
                'role' => 'karenderia_owner',
                'username' => 'lindacruz',
                'verified' => true,
                'email_verified_at' => now(),
                'phone_number' => '+639987654321',
                'address' => '456 Dela Rosa Avenue, Quezon City',
            ]
        );

        User::firstOrCreate(
            ['email' => 'owner3@kaplato.com'],
            [
                'name' => 'Roberto Dela Cruz',
                'email' => 'owner3@kaplato.com',
                'password' => Hash::make('owner123'),
                'role' => 'karenderia_owner',
                'username' => 'robertodc',
                'verified' => true,
                'email_verified_at' => now(),
                'phone_number' => '+639555123456',
                'address' => '789 Bonifacio Street, Pasig City',
            ]
        );

        // Keep the original owner for compatibility
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
