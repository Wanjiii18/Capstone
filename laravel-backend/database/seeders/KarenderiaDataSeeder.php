<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karenderia;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class KarenderiaDataSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the karenderia owners
        $owner1 = User::where('email', 'owner1@kaplato.com')->first();
        $owner2 = User::where('email', 'owner2@kaplato.com')->first();
        $owner3 = User::where('email', 'owner3@kaplato.com')->first();
        $fallbackOwner = User::where('email', 'owner@kaplato.com')->first();
        $customer = User::where('email', 'customer@kaplato.com')->first();
        
        if (!$owner1 || !$owner2 || !$owner3 || !$customer) {
            $this->command->error('Please run AdminUserSeeder first!');
            return;
        }

        // 1. ACTIVE Karenderia - Maria's Kitchen (owner1)
        $karenderia1 = Karenderia::firstOrCreate([
            'name' => 'Lola Maria\'s Kitchen',
            'owner_id' => $owner1->id,
        ], [
            'business_name' => 'Lola Maria\'s Kitchen Business',
            'description' => 'Authentic Filipino home-cooked meals served with love. Family recipes passed down through generations.',
            'address' => '123 Rizal Street, Makati City, Metro Manila',
            'city' => 'Makati City',
            'province' => 'Metro Manila',
            'phone' => '+639123456789',
            'email' => 'lolakitchen@example.com',
            'business_email' => 'business@lolakitchen.com',
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'opening_time' => '06:00:00',
            'closing_time' => '21:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'status' => 'active',
            'delivery_fee' => 50.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'average_rating' => 4.5,
            'total_reviews' => 87
        ]);

        // 2. ACTIVE Karenderia - Linda's Lutong Bahay (owner2)
        $karenderia2 = Karenderia::firstOrCreate([
            'name' => 'Tita Linda\'s Lutong Bahay',
            'owner_id' => $owner2->id,
        ], [
            'business_name' => 'Tita Linda\'s Lutong Bahay Enterprise',
            'description' => 'Traditional Filipino comfort food made fresh daily',
            'address' => '456 Dela Rosa Avenue, Quezon City, Metro Manila',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'phone' => '+639987654321',
            'email' => 'titalinda@example.com',
            'business_email' => 'business@titalinda.com',
            'latitude' => 14.6760,
            'longitude' => 121.0437,
            'opening_time' => '07:00:00',
            'closing_time' => '20:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            'status' => 'active',
            'delivery_fee' => 45.00,
            'delivery_time_minutes' => 25,
            'accepts_cash' => true,
            'accepts_online_payment' => false,
            'average_rating' => 4.2,
            'total_reviews' => 64
        ]);

        // 3. PENDING Karenderia - Roberto's Place (owner3) - NOT YET APPROVED
        $karenderia3 = Karenderia::firstOrCreate([
            'name' => 'Kuya Roberto\'s Place',
            'owner_id' => $owner3->id,
        ], [
            'business_name' => 'Kuya Roberto\'s Food Place',
            'description' => 'Grilled specialties and rice meals',
            'address' => '789 Bonifacio Street, Pasig City, Metro Manila',
            'city' => 'Pasig City',
            'province' => 'Metro Manila',
            'phone' => '+639555123456',
            'email' => 'kuyaroberto@example.com',
            'business_email' => 'business@kuyaroberto.com',
            'latitude' => 14.5764,
            'longitude' => 121.0851,
            'opening_time' => '10:00:00',
            'closing_time' => '22:00:00',
            'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'status' => 'pending',  // This karenderia is not yet approved
            'delivery_fee' => 35.00,
            'delivery_time_minutes' => 20,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'average_rating' => 0,
            'total_reviews' => 0
        ]);

        // Create menu items for ACTIVE karenderias only
        $this->createMariaKitchenMenu($karenderia1);
        $this->createLindaLutongBahayMenu($karenderia2);
        
        // Note: Roberto's Place (pending) doesn't have menu items yet
        // This simulates a new karenderia waiting for approval

        $this->command->info('Sample karenderia data created successfully!');
        $this->command->info('- Lola Maria\'s Kitchen (ACTIVE) - 4 menu items');
        $this->command->info('- Tita Linda\'s Lutong Bahay (ACTIVE) - 2 menu items');
        $this->command->info('- Kuya Roberto\'s Place (PENDING) - 0 menu items');
    }

    private function createMariaKitchenMenu($karenderia)
    {
        $menuItems = [
            [
                'name' => 'Adobong Manok',
                'description' => 'Classic Filipino chicken adobo cooked in soy sauce and vinegar',
                'price' => 120.00,
                'cost_price' => 70.00,
                'category' => 'main_course',
                'is_available' => true,
                'is_featured' => true,
                'preparation_time_minutes' => 20,
                'calories' => 350,
                'ingredients' => ['chicken', 'soy sauce', 'vinegar', 'garlic', 'bay leaves'],
                'allergens' => ['soy'],
                'spice_level' => 2,
                'average_rating' => 4.7,
                'total_reviews' => 45,
                'total_orders' => 123
            ],
            [
                'name' => 'Sinigang na Baboy',
                'description' => 'Sour pork soup with vegetables',
                'price' => 150.00,
                'cost_price' => 90.00,
                'category' => 'main_course',
                'is_available' => true,
                'is_featured' => true,
                'preparation_time_minutes' => 25,
                'calories' => 280,
                'ingredients' => ['pork', 'tamarind', 'kangkong', 'radish', 'tomatoes'],
                'allergens' => [],
                'spice_level' => 1,
                'average_rating' => 4.5,
                'total_reviews' => 32,
                'total_orders' => 89
            ],
            [
                'name' => 'Pancit Canton',
                'description' => 'Stir-fried noodles with vegetables and meat',
                'price' => 100.00,
                'cost_price' => 60.00,
                'category' => 'main_course',
                'is_available' => true,
                'preparation_time_minutes' => 15,
                'calories' => 320,
                'ingredients' => ['canton noodles', 'cabbage', 'carrots', 'pork', 'shrimp'],
                'allergens' => ['gluten', 'shellfish'],
                'spice_level' => 1,
                'average_rating' => 4.3,
                'total_reviews' => 28,
                'total_orders' => 76
            ],
            [
                'name' => 'Halo-Halo',
                'description' => 'Filipino shaved ice dessert with mixed ingredients',
                'price' => 80.00,
                'cost_price' => 45.00,
                'category' => 'dessert',
                'is_available' => true,
                'preparation_time_minutes' => 10,
                'calories' => 250,
                'ingredients' => ['shaved ice', 'ube', 'leche flan', 'beans', 'corn'],
                'allergens' => ['dairy'],
                'spice_level' => 0,
                'average_rating' => 4.6,
                'total_reviews' => 41,
                'total_orders' => 95
            ]
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate([
                'karenderia_id' => $karenderia->id,
                'name' => $item['name']
            ], array_merge($item, ['karenderia_id' => $karenderia->id]));
        }
    }

    private function createLindaLutongBahayMenu($karenderia)
    {
        $menuItems = [
            [
                'name' => 'Beef Kare-Kare',
                'description' => 'Oxtail and vegetables in peanut sauce',
                'price' => 180.00,
                'cost_price' => 110.00,
                'category' => 'main_course',
                'is_available' => true,
                'is_featured' => true,
                'preparation_time_minutes' => 30,
                'calories' => 420,
                'ingredients' => ['oxtail', 'peanut sauce', 'eggplant', 'string beans'],
                'allergens' => ['peanuts'],
                'spice_level' => 1,
                'average_rating' => 4.4,
                'total_reviews' => 25,
                'total_orders' => 67
            ],
            [
                'name' => 'Lumpia Shanghai',
                'description' => 'Filipino spring rolls with ground pork',
                'price' => 90.00,
                'cost_price' => 50.00,
                'category' => 'appetizer',
                'is_available' => true,
                'preparation_time_minutes' => 12,
                'calories' => 180,
                'ingredients' => ['ground pork', 'carrots', 'spring roll wrapper'],
                'allergens' => ['gluten'],
                'spice_level' => 1,
                'average_rating' => 4.5,
                'total_reviews' => 33,
                'total_orders' => 88
            ]
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate([
                'karenderia_id' => $karenderia->id,
                'name' => $item['name']
            ], array_merge($item, ['karenderia_id' => $karenderia->id]));
        }
    }
}
