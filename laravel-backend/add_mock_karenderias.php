<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Adding 4 Mock Karenderias to Map ===\n";

try {
    // First, let's make sure we have owner users
    $owner1 = \App\Models\User::firstOrCreate(
        ['email' => 'owner1@kaplato.com'],
        [
            'name' => 'Maria Santos',
            'email' => 'owner1@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]
    );

    $owner2 = \App\Models\User::firstOrCreate(
        ['email' => 'owner2@kaplato.com'],
        [
            'name' => 'Juan Dela Cruz',
            'email' => 'owner2@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]
    );

    $owner3 = \App\Models\User::firstOrCreate(
        ['email' => 'owner3@kaplato.com'],
        [
            'name' => 'Ana Reyes',
            'email' => 'owner3@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]
    );

    $owner4 = \App\Models\User::firstOrCreate(
        ['email' => 'owner4@kaplato.com'],
        [
            'name' => 'Pedro Garcia',
            'email' => 'owner4@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]
    );

    echo "✅ Created owner users\n";

    // Now create 4 karenderias with proper coordinates (Manila area)
    $karenderias = [
        [
            'name' => "Maria's Lutong Bahay",
            'business_name' => "Maria's Lutong Bahay",
            'description' => 'Authentic Filipino home-cooked meals with traditional recipes passed down through generations.',
            'address' => '123 Rizal Street, Barangay San Antonio',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'phone' => '+639171234567',
            'business_email' => 'maria@lutongbahay.com',
            'owner_id' => $owner1->id,
            'latitude' => 14.5995,  // Manila coordinates
            'longitude' => 120.9842,
            'opening_time' => '06:00',
            'closing_time' => '21:00',
            'operating_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'status' => 'active',
            'average_rating' => 4.5,
            'total_reviews' => 45,
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'approved_at' => now(),
        ],
        [
            'name' => "Juan's Tindahan at Kainan",
            'business_name' => "Juan's Tindahan at Kainan",
            'description' => 'Fresh seafood and grilled specialties with rice meals perfect for any time of day.',
            'address' => '456 Del Pilar Street, Barangay Malate',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'phone' => '+639182345678',
            'business_email' => 'juan@tindahan.com',
            'owner_id' => $owner2->id,
            'latitude' => 14.5764,  // Malate area
            'longitude' => 120.9851,
            'opening_time' => '07:00',
            'closing_time' => '22:00',
            'operating_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'status' => 'active',
            'average_rating' => 4.2,
            'total_reviews' => 38,
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 25,
            'accepts_cash' => true,
            'accepts_online_payment' => false,
            'approved_at' => now(),
        ],
        [
            'name' => "Ana's Masarap na Pagkain",
            'business_name' => "Ana's Masarap na Pagkain",
            'description' => 'Budget-friendly Filipino comfort food with generous servings and home-style cooking.',
            'address' => '789 Quezon Avenue, Barangay Paligsahan',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'phone' => '+639193456789',
            'business_email' => 'ana@masarap.com',
            'owner_id' => $owner3->id,
            'latitude' => 14.6349,  // Quezon City
            'longitude' => 121.0205,
            'opening_time' => '05:30',
            'closing_time' => '20:00',
            'operating_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'status' => 'active',
            'average_rating' => 4.7,
            'total_reviews' => 62,
            'delivery_fee' => 20.00,
            'delivery_time_minutes' => 35,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'approved_at' => now(),
        ],
        [
            'name' => "Pedro's Ihaw-Ihaw Corner",
            'business_name' => "Pedro's Ihaw-Ihaw Corner",
            'description' => 'Grilled specialties and barbecue with ice-cold drinks, perfect for late night cravings.',
            'address' => '321 Taft Avenue, Barangay 690',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'phone' => '+639204567890',
            'business_email' => 'pedro@ihawihaw.com',
            'owner_id' => $owner4->id,
            'latitude' => 14.5886,  // Taft area
            'longitude' => 120.9823,
            'opening_time' => '15:00',
            'closing_time' => '02:00',
            'operating_days' => ['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'status' => 'active',
            'average_rating' => 4.3,
            'total_reviews' => 29,
            'delivery_fee' => 35.00,
            'delivery_time_minutes' => 40,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
            'approved_at' => now(),
        ],
    ];

    echo "\nCreating karenderias...\n";

    foreach ($karenderias as $index => $karenderiaData) {
        $karenderia = \App\Models\Karenderia::create($karenderiaData);
        echo "✅ Created: {$karenderia->name} (ID: {$karenderia->id})\n";
        echo "   📍 Location: {$karenderia->latitude}, {$karenderia->longitude}\n";
        echo "   📧 Owner: {$karenderia->owner->name} ({$karenderia->owner->email})\n";
        echo "   ⭐ Rating: {$karenderia->average_rating}/5 ({$karenderia->total_reviews} reviews)\n\n";
    }

    echo "=== Summary ===\n";
    $totalKarenderias = \App\Models\Karenderia::count();
    $withCoordinates = \App\Models\Karenderia::whereNotNull('latitude')->whereNotNull('longitude')->count();
    
    echo "✅ Total Karenderias: {$totalKarenderias}\n";
    echo "✅ With Map Coordinates: {$withCoordinates}\n";
    echo "✅ All karenderias are now ready to display on the map!\n";

    echo "\n=== Map Coverage Area ===\n";
    echo "📍 Manila: Maria's & Juan's & Pedro's\n";
    echo "📍 Quezon City: Ana's\n";
    echo "🗺️  Coordinates range: Lat 14.5764-14.6349, Lng 120.9823-121.0205\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
