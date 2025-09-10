<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Adding the missing 4th Karenderia ===\n";

try {
    $owner = \App\Models\User::where('email', 'owner@kaplato.com')->first();
    
    // Add the 4th karenderia with correct status
    $karenderia = \App\Models\Karenderia::create([
        'name' => "Kuya Roberto's Place",
        'description' => 'Grilled specialties, barbecue, and Filipino favorites',
        'address' => '789 Burgos Street, Mandaue City, Cebu',
        'phone' => '+639204567890',
        'email' => 'kuya.roberto@place.com',
        'owner_id' => $owner->id,
        'latitude' => 10.3378,
        'longitude' => 123.9445,
        'opening_time' => '15:00:00',
        'closing_time' => '23:00:00',
        'operating_days' => json_encode(['Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
        'status' => 'inactive', // Use 'inactive' instead of 'closed'
        'average_rating' => 4.4,
        'total_reviews' => 29,
        'delivery_fee' => 30.00,
        'delivery_time_minutes' => 35,
        'accepts_cash' => true,
        'accepts_online_payment' => true,
    ]);

    echo "✅ 🔴 {$karenderia->name} (Status: {$karenderia->status})\n";
    echo "   📍 {$karenderia->latitude}, {$karenderia->longitude}\n";
    echo "   📍 {$karenderia->address}\n";

    echo "\n=== Final Summary ===\n";
    $total = \App\Models\Karenderia::count();
    $active = \App\Models\Karenderia::where('status', 'active')->count();
    $inactive = \App\Models\Karenderia::where('status', 'inactive')->count();
    
    echo "✅ Total Karenderias: {$total}\n";
    echo "🟢 Active: {$active}\n";
    echo "🔴 Inactive: {$inactive}\n";
    echo "📍 All located in: Mandaue City, Cebu, Philippines\n";
    echo "🗺️  Ready for map display!\n";

    echo "\n=== All Karenderias ===\n";
    $allKarenderias = \App\Models\Karenderia::all();
    foreach($allKarenderias as $k) {
        $statusIcon = $k->status === 'active' ? '🟢' : '🔴';
        echo "{$statusIcon} {$k->name} - Rating: {$k->average_rating}/5\n";
        echo "   📍 Lat: {$k->latitude}, Lng: {$k->longitude}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
