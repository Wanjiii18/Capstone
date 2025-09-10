<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Adding Karenderias in Mandaue City, Cebu (Fixed) ===\n";

try {
    // Clear existing karenderias safely
    \App\Models\Karenderia::query()->delete();
    echo "✅ Cleared existing karenderias\n";

    // Get existing owner or create one
    $owner = \App\Models\User::where('email', 'owner@kaplato.com')->first();
    
    if (!$owner) {
        $owner = \App\Models\User::create([
            'name' => 'Test Owner',
            'email' => 'owner@kaplato.com',
            'password' => \Illuminate\Support\Facades\Hash::make('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]);
        echo "✅ Created owner user\n";
    }

    // Create karenderias in Mandaue City, Cebu (matching your image)
    $karenderias = [
        [
            'name' => "Lola Maria's Kitchen",
            'description' => 'Authentic Filipino traditional cuisine with home-cooked flavors',
            'address' => '123 Mabini Street, Mandaue City, Cebu',
            'phone' => '+639171234567',
            'email' => 'lola.maria@kitchen.com',
            'owner_id' => $owner->id,
            'latitude' => 10.3157,  // Mandaue City, Cebu coordinates
            'longitude' => 123.9227,
            'opening_time' => '06:00:00',
            'closing_time' => '21:00:00',
            'operating_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
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
            'description' => 'Fresh seafood and Filipino comfort food specialties',
            'address' => '321 Rizal Avenue, Mandaue City, Cebu',
            'phone' => '+639182345678',
            'email' => 'nanay.cora@carinderia.com',
            'owner_id' => $owner->id,
            'latitude' => 10.3289,  // Different area in Mandaue
            'longitude' => 123.9321,
            'opening_time' => '07:00:00',
            'closing_time' => '22:00:00',
            'operating_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
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
            'description' => 'Home-cooked Filipino meals with generous servings and love',
            'address' => '456 Plaridel Street, Mandaue City, Cebu',
            'phone' => '+639193456789',
            'email' => 'tita.linda@lutongbahay.com',
            'owner_id' => $owner->id,
            'latitude' => 10.3045,  # South Mandaue area
            'longitude' => 123.9156,
            'opening_time' => '05:30:00',
            'closing_time' => '20:00:00',
            'operating_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
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
            'description' => 'Grilled specialties, barbecue, and Filipino favorites',
            'address' => '789 Burgos Street, Mandaue City, Cebu',
            'phone' => '+639204567890',
            'email' => 'kuya.roberto@place.com',
            'owner_id' => $owner->id,
            'latitude' => 10.3378,  # North Mandaue area
            'longitude' => 123.9445,
            'opening_time' => '15:00:00',
            'closing_time' => '23:00:00',
            'operating_days' => json_encode(['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'status' => 'closed', # This one shows as closed in your image
            'average_rating' => 4.4,
            'total_reviews' => 29,
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 35,
            'accepts_cash' => true,
            'accepts_online_payment' => true,
        ],
    ];

    echo "Creating karenderias in Mandaue City, Cebu...\n\n";

    foreach ($karenderias as $index => $karenderiaData) {
        $karenderia = \App\Models\Karenderia::create($karenderiaData);
        $statusIcon = $karenderia->status === 'active' ? '🟢' : '🔴';
        echo "✅ {$statusIcon} {$karenderia->name}\n";
        echo "   📍 {$karenderia->latitude}, {$karenderia->longitude}\n";
        echo "   📍 {$karenderia->address}\n";
        echo "   ⭐ {$karenderia->average_rating}/5 ({$karenderia->total_reviews} reviews)\n";
        echo "   🚛 ₱{$karenderia->delivery_fee} delivery\n";
        echo "   ⏰ {$karenderia->opening_time} - {$karenderia->closing_time}\n\n";
    }

    echo "=== SUCCESS! ===\n";
    $total = \App\Models\Karenderia::count();
    $active = \App\Models\Karenderia::where('status', 'active')->count();
    $closed = \App\Models\Karenderia::where('status', 'closed')->count();
    
    echo "✅ Total Karenderias: {$total}\n";
    echo "🟢 Active: {$active}\n";
    echo "🔴 Closed: {$closed}\n";
    echo "📍 Location: Mandaue City, Cebu, Philippines\n";
    echo "🗺️  Ready for map display!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
