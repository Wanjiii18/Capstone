<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Adding Karenderias in Mandaue City, Cebu ===\n";

try {
    // First check the actual table structure
    echo "Checking karenderias table structure...\n";
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('karenderias');
    echo "Available columns: " . implode(', ', $columns) . "\n\n";

    // Clear existing karenderias
    \App\Models\Karenderia::truncate();
    echo "Cleared existing karenderias\n";

    // Get existing owners or create them
    $owner1 = \App\Models\User::where('email', 'owner@kaplato.com')->first() ?? 
              \App\Models\User::where('role', 'karenderia_owner')->first();

    if (!$owner1) {
        $owner1 = \App\Models\User::create([
            'name' => 'Maria Lola',
            'email' => 'lola.maria@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]);
    }

    // Cebu karenderias with correct coordinates (Mandaue City, Cebu area)
    $karenderias = [
        [
            'name' => "Lola Maria's Kitchen",
            'description' => 'Authentic Filipino traditional cuisine with home-cooked flavors',
            'address' => '123 Mabini Street, Mandaue City, Cebu',
            'phone' => '+639171234567',
            'email' => 'lola.maria@kitchen.com',
            'owner_id' => $owner1->id,
            'latitude' => 10.3157,  // Mandaue City coordinates
            'longitude' => 123.9227,
            'opening_time' => '06:00',
            'closing_time' => '21:00',
            'status' => 'active',
            'average_rating' => 4.8,
            'total_reviews' => 45,
            'delivery_fee' => 20.00,
            'delivery_time_minutes' => 25,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
        ],
        [
            'name' => "Nanay Cora's Carinderia",
            'description' => 'Fresh seafood and Filipino comfort food',
            'address' => '321 Rizal Avenue, Mandaue City, Cebu',
            'phone' => '+639182345678',
            'email' => 'nanay.cora@carinderia.com',
            'owner_id' => $owner1->id,
            'latitude' => 10.3289,  // Slightly different area in Mandaue
            'longitude' => 123.9321,
            'opening_time' => '07:00',
            'closing_time' => '22:00',
            'status' => 'active',
            'average_rating' => 4.7,
            'total_reviews' => 38,
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 28,
            'accepts_cash' => true,
            'accepts_online_payment' => false,
        ],
        [
            'name' => "Tita Linda's Lutong Bahay",
            'description' => 'Home-cooked Filipino meals with generous servings',
            'address' => '456 Plaridel Street, Mandaue City, Cebu',
            'phone' => '+639193456789',
            'email' => 'tita.linda@lutongbahay.com',
            'owner_id' => $owner1->id,
            'latitude' => 10.3045,  // Another area in Mandaue
            'longitude' => 123.9156,
            'opening_time' => '05:30',
            'closing_time' => '20:00',
            'status' => 'active',
            'average_rating' => 4.6,
            'total_reviews' => 62,
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
        ],
        [
            'name' => "Kuya Roberto's Place",
            'description' => 'Grilled specialties and Filipino barbecue',
            'address' => '789 Burgos Street, Mandaue City, Cebu',
            'phone' => '+639204567890',
            'email' => 'kuya.roberto@place.com',
            'owner_id' => $owner1->id,
            'latitude' => 10.3378,  // North Mandaue area
            'longitude' => 123.9445,
            'opening_time' => '15:00',
            'closing_time' => '23:00',
            'status' => 'closed', // This one shows as closed in your image
            'average_rating' => 4.4,
            'total_reviews' => 29,
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 35,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
        ],
    ];

    echo "Creating karenderias in Mandaue City, Cebu...\n";

    foreach ($karenderias as $index => $karenderiaData) {
        $karenderia = \App\Models\Karenderia::create($karenderiaData);
        echo "✅ Created: {$karenderia->name}\n";
        echo "   📍 Location: {$karenderia->latitude}, {$karenderia->longitude}\n";
        echo "   📧 Address: {$karenderia->address}\n";
        echo "   ⭐ Rating: {$karenderia->average_rating}/5\n";
        echo "   🚛 Delivery: ₱{$karenderia->delivery_fee}\n\n";
    }

    echo "=== Summary ===\n";
    $totalKarenderias = \App\Models\Karenderia::count();
    $activeKarenderias = \App\Models\Karenderia::where('status', 'active')->count();
    
    echo "✅ Total Karenderias: {$totalKarenderias}\n";
    echo "✅ Active Karenderias: {$activeKarenderias}\n";
    echo "📍 All located in: Mandaue City, Cebu, Philippines\n";
    echo "🗺️  Coordinates range: Lat 10.3045-10.3378, Lng 123.9156-123.9445\n";

    echo "\n=== Map Coverage Area ===\n";
    echo "📍 Mandaue City, Cebu area - matches your location!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Let me check what columns are available...\n";
}
