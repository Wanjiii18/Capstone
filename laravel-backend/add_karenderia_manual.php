<?php
/**
 * 🏪 Direct Karenderia Location Entry Tool
 * ========================================
 * 
 * This script allows you to manually add karenderia locations directly
 * without going through the registration form. Useful for testing.
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏪 Direct Karenderia Location Entry\n";
echo "==================================\n\n";

// Function to add a karenderia location
function addKarenderiaLocation($data) {
    try {
        // First, create or find the owner
        $owner = \App\Models\User::firstOrCreate(
            ['email' => $data['owner_email']],
            [
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'karenderia_owner',
                'verified' => true, // Pre-approve for testing
                'application_status' => 'approved',
                'phone_number' => $data['owner_phone'] ?? '09123456789'
            ]
        );

        // Create the karenderia
        $karenderia = \App\Models\Karenderia::create([
            'name' => $data['name'],
            'business_name' => $data['business_name'] ?? $data['name'],
            'description' => $data['description'],
            'address' => $data['address'],
            'city' => $data['city'],
            'province' => $data['province'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'business_email' => $data['email'] ?? null,
            'owner_id' => $owner->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'opening_time' => $data['opening_time'] ?? '06:00',
            'closing_time' => $data['closing_time'] ?? '20:00',
            'operating_days' => $data['operating_days'] ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'status' => 'active', // Pre-approve for testing
            'delivery_fee' => $data['delivery_fee'] ?? 25.00,
            'delivery_time_minutes' => $data['delivery_time_minutes'] ?? 30,
            'accepts_cash' => true,
            'accepts_online_payment' => false,
            'average_rating' => 4.0,
            'total_reviews' => 0,
            'approved_at' => now(),
            'approved_by' => 1 // Assuming admin ID 1
        ]);

        return $karenderia;
    } catch (Exception $e) {
        throw new Exception("Failed to create karenderia: " . $e->getMessage());
    }
}

// Example locations you can add
$locations = [
    [
        'name' => 'Mang Inasal Lahug',
        'description' => 'Famous grilled chicken and unlimited rice',
        'address' => 'IT Park, Lahug, Cebu City',
        'city' => 'Cebu City',
        'province' => 'Cebu',
        'latitude' => 10.3267,
        'longitude' => 123.9073,
        'phone' => '09171234567',
        'email' => 'lahug@manginasal.com',
        'owner_name' => 'Juan Dela Cruz',
        'owner_email' => 'juan.manginasal@email.com',
        'owner_phone' => '09171234567'
    ],
    [
        'name' => 'Lola Fely\'s Carinderia',
        'description' => 'Traditional home-cooked Filipino meals',
        'address' => 'Colon Street, Cebu City',
        'city' => 'Cebu City',
        'province' => 'Cebu',
        'latitude' => 10.2960,
        'longitude' => 123.9015,
        'phone' => '09181234567',
        'owner_name' => 'Felicia Santos',
        'owner_email' => 'fely.carinderia@email.com',
        'owner_phone' => '09181234567'
    ],
    [
        'name' => 'Kuya\'s Lutong Bahay',
        'description' => 'Affordable Filipino comfort food',
        'address' => 'Capitol Site, Cebu City',
        'city' => 'Cebu City',
        'province' => 'Cebu',
        'latitude' => 10.3120,
        'longitude' => 123.8932,
        'phone' => '09191234567',
        'owner_name' => 'Roberto Cruz',
        'owner_email' => 'kuya.lutongbahay@email.com',
        'owner_phone' => '09191234567'
    ]
];

echo "Available locations to add:\n";
foreach ($locations as $index => $location) {
    echo ($index + 1) . ". {$location['name']} - {$location['address']}\n";
}

echo "\nChoose an option:\n";
echo "1. Add all locations above\n";
echo "2. Add specific location (enter number 1-" . count($locations) . ")\n";
echo "3. Add custom location (manual entry)\n";
echo "4. List current karenderias\n";
echo "5. Clear all test karenderias\n";

// For automatic testing, let's add the first location
echo "\n🚀 Adding first location for demonstration...\n";

try {
    $karenderia = addKarenderiaLocation($locations[0]);
    
    echo "✅ Successfully added karenderia!\n";
    echo "• ID: {$karenderia->id}\n";
    echo "• Name: {$karenderia->name}\n";
    echo "• Address: {$karenderia->address}\n";
    echo "• Coordinates: {$karenderia->latitude}, {$karenderia->longitude}\n";
    echo "• Status: {$karenderia->status}\n";
    echo "• Owner: {$karenderia->owner->name}\n\n";
    
    echo "🗺️ This location will now appear in nearby search!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n📋 Current active karenderias:\n";
$karenderias = \App\Models\Karenderia::where('status', 'active')->with('owner')->get();
foreach ($karenderias as $kar) {
    echo "• {$kar->name} ({$kar->address}) - Owner: {$kar->owner->name}\n";
}

echo "\n💡 Pro Tips:\n";
echo "• Use the web form for real owner registrations\n";
echo "• Use this script for quick testing and demos\n";
echo "• All locations added here are pre-approved (status: active)\n";
echo "• Coordinates should be in decimal degrees format\n";
echo "• Make sure coordinates are within the Philippines!\n\n";

echo "🌐 Test your locations at: http://127.0.0.1:8000/api/karenderias/nearby?latitude=10.3157&longitude=123.8854&radius=5000\n";