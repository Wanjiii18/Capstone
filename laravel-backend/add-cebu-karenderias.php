<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Karenderia;
use App\Models\User;

echo "=== Adding Cebu Karenderias for Map Display ===\n";

try {
    // Get a test owner
    $owner = User::where('role', 'karenderia_owner')->first();
    if (!$owner) {
        echo "❌ No owner found! Creating one...\n";
        $owner = User::create([
            'name' => 'Test Owner',
            'email' => 'owner@kaplato.com',
            'password' => bcrypt('owner123'),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
        ]);
    }

    // Clear existing karenderias
    Karenderia::query()->delete();
    echo "🗑️ Cleared existing karenderias\n";

    // Cebu karenderias for map display
    $karenderias = [
        [
            'name' => 'Lola Mila\'s Karenderia',
            'description' => 'Authentic Filipino comfort food',
            'address' => 'A.C. Cortes Avenue, Mandaue City, Cebu',
            'phone' => '+63 32 345 6789',
            'email' => 'lolamila@gmail.com',
            'latitude' => 10.3234,
            'longitude' => 123.9312,
            'status' => 'active',
            'owner_id' => $owner->id,
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 25,
            'average_rating' => 4.5,
            'total_reviews' => 120
        ],
        [
            'name' => 'Tita Rosa\'s Kitchen',
            'description' => 'Home-style Filipino dishes',
            'address' => 'Plaridel Street, Mandaue City, Cebu',
            'phone' => '+63 32 456 7890',
            'email' => 'titarosa@gmail.com',
            'latitude' => 10.3198,
            'longitude' => 123.9278,
            'status' => 'active',
            'owner_id' => $owner->id,
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 30,
            'average_rating' => 4.2,
            'total_reviews' => 89
        ],
        [
            'name' => 'Kuya Ben\'s Lutong Bahay',
            'description' => 'Fresh seafood and Filipino favorites',
            'address' => 'Subangdaku, Mandaue City, Cebu',
            'phone' => '+63 32 567 8901',
            'email' => 'kuyaben@gmail.com',
            'latitude' => 10.3156,
            'longitude' => 123.9234,
            'status' => 'active',
            'owner_id' => $owner->id,
            'delivery_fee' => 35.00,
            'delivery_time_minutes' => 35,
            'average_rating' => 4.7,
            'total_reviews' => 156
        ],
        [
            'name' => 'Ate Joy\'s Carinderia',
            'description' => 'Budget-friendly Filipino meals',
            'address' => 'Banilad, Mandaue City, Cebu',
            'phone' => '+63 32 678 9012',
            'email' => 'atejoy@gmail.com',
            'latitude' => 10.3289,
            'longitude' => 123.9345,
            'status' => 'active',
            'owner_id' => $owner->id,
            'delivery_fee' => 20.00,
            'delivery_time_minutes' => 20,
            'average_rating' => 4.3,
            'total_reviews' => 98
        ]
    ];

    foreach ($karenderias as $data) {
        $karenderia = Karenderia::create($data);
        echo "✅ Created: {$karenderia->name} at {$karenderia->address}\n";
        echo "   📍 Coordinates: {$karenderia->latitude}, {$karenderia->longitude}\n";
    }

    echo "\n🎉 SUCCESS: All 4 Cebu karenderias created!\n";
    echo "📍 All located in Mandaue City, Cebu area\n";
    echo "🗺️ Ready for map display!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
