<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karenderia;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create sample customers
        $customers = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@email.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '09123456789',
                'email_verified_at' => now(),
                'height' => 170,
                'weight' => 70,
                'activity_level' => 'moderate',
                'fitness_goal' => 'maintenance',
                'dietary_restrictions' => json_encode(['vegetarian']),
                'allergies' => json_encode(['nuts']),
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '09234567890',
                'email_verified_at' => now(),
                'height' => 160,
                'weight' => 55,
                'activity_level' => 'light',
                'fitness_goal' => 'weight_loss',
                'dietary_restrictions' => json_encode([]),
                'allergies' => json_encode(['shellfish']),
            ],
            [
                'name' => 'Pedro Garcia',
                'email' => 'pedro.garcia@email.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '09345678901',
                'email_verified_at' => now(),
                'height' => 175,
                'weight' => 80,
                'activity_level' => 'very_active',
                'fitness_goal' => 'muscle_gain',
                'dietary_restrictions' => json_encode(['low_carb']),
                'allergies' => json_encode([]),
            ],
            [
                'name' => 'Ana Reyes',
                'email' => 'ana.reyes@email.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '09456789012',
                'email_verified_at' => null, // Unverified user
                'height' => 165,
                'weight' => 60,
                'activity_level' => 'moderate',
                'fitness_goal' => 'maintenance',
                'dietary_restrictions' => json_encode(['gluten_free']),
                'allergies' => json_encode(['dairy']),
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos.mendoza@email.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '09567890123',
                'email_verified_at' => now(),
                'height' => 180,
                'weight' => 85,
                'activity_level' => 'active',
                'fitness_goal' => 'weight_loss',
                'dietary_restrictions' => json_encode([]),
                'allergies' => json_encode([]),
            ]
        ];

        foreach ($customers as $customerData) {
            User::create($customerData);
        }

        // Create sample karenderia owners
        $owners = [
            [
                'name' => 'Aling Rosa',
                'email' => 'rosa.karenderia@email.com',
                'password' => Hash::make('password123'),
                'role' => 'karenderia_owner',
                'phone_number' => '09678901234',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Kuya Ben',
                'email' => 'ben.foodstall@email.com',
                'password' => Hash::make('password123'),
                'role' => 'karenderia_owner',
                'phone_number' => '09789012345',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Tita Carmen',
                'email' => 'carmen.eatery@email.com',
                'password' => Hash::make('password123'),
                'role' => 'karenderia_owner',
                'phone_number' => '09890123456',
                'email_verified_at' => null, // Unverified owner
            ]
        ];

        foreach ($owners as $ownerData) {
            $owner = User::create($ownerData);
            
            // Create karenderia applications for some owners
            if ($owner->email !== 'carmen.eatery@email.com') {
                $karenderiaData = [
                    'owner_id' => $owner->id,
                    'name' => $this->getBusinessName($owner->name),
                    'business_name' => $this->getBusinessName($owner->name),
                    'description' => 'Authentic Filipino home-cooked meals',
                    'address' => $this->getAddress(),
                    'city' => $this->getCity(),
                    'province' => 'Metro Manila',
                    'phone' => $owner->phone_number,
                    'business_email' => $owner->email,
                    'opening_time' => '07:00:00',
                    'closing_time' => '20:00:00',
                    'operating_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
                    'status' => $owner->name === 'Aling Rosa' ? 'approved' : 'pending',
                    'delivery_fee' => rand(20, 50),
                    'delivery_time_minutes' => rand(30, 60),
                    'accepts_cash' => true,
                    'accepts_online_payment' => rand(0, 1),
                    'approved_at' => $owner->name === 'Aling Rosa' ? now() : null,
                    'approved_by' => $owner->name === 'Aling Rosa' ? 1 : null, // Admin user ID
                ];

                Karenderia::create($karenderiaData);
            }
        }

        $this->command->info('Sample customers and karenderia owners created successfully!');
        $this->command->info('Test customer accounts:');
        $this->command->info('- juan.delacruz@email.com / password123');
        $this->command->info('- maria.santos@email.com / password123');
        $this->command->info('- pedro.garcia@email.com / password123');
        $this->command->info('- ana.reyes@email.com / password123 (unverified)');
        $this->command->info('- carlos.mendoza@email.com / password123');
        $this->command->info('');
        $this->command->info('Test karenderia owner accounts:');
        $this->command->info('- rosa.karenderia@email.com / password123 (approved business)');
        $this->command->info('- ben.foodstall@email.com / password123 (pending business)');
        $this->command->info('- carmen.eatery@email.com / password123 (no business yet)');
    }

    private function getBusinessName($ownerName)
    {
        $businessNames = [
            'Aling Rosa' => "Rosa's Karenderia",
            'Kuya Ben' => "Ben's Food Corner",
            'Tita Carmen' => "Carmen's Eatery"
        ];

        return $businessNames[$ownerName] ?? $ownerName . "'s Karenderia";
    }

    private function getAddress()
    {
        $addresses = [
            '123 Rizal Street, Barangay San Antonio',
            '456 Bonifacio Avenue, Barangay Poblacion',
            '789 Luna Road, Barangay Magsaysay',
            '321 Mabini Street, Barangay Centro',
            '654 Del Pilar Avenue, Barangay Santo Niño'
        ];

        return $addresses[array_rand($addresses)];
    }

    private function getCity()
    {
        $cities = [
            'Quezon City',
            'Manila',
            'Makati',
            'Pasig',
            'Mandaluyong'
        ];

        return $cities[array_rand($cities)];
    }
}
