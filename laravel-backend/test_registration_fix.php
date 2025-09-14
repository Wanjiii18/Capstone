<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Karenderia Registration Fix ===\n\n";

// Simulate the registration data
$testData = [
    'name' => 'Test User Registration',
    'email' => 'testregfix@example.com',
    'password' => 'password123',
    'business_name' => 'Test Kitchen Registration',
    'description' => 'A test kitchen to verify registration works',
    'address' => '123 Test Street, Test City',
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'phone' => '+639123456789',
    'business_email' => 'business@example.com',
    'delivery_fee' => 25,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false,
];

try {
    // Check if email already exists
    $existingUser = DB::table('users')->where('email', $testData['email'])->first();
    if ($existingUser) {
        echo "Deleting existing test user...\n";
        DB::table('karenderias')->where('owner_id', $existingUser->id)->delete();
        DB::table('users')->where('id', $existingUser->id)->delete();
    }
    
    // Create user
    $user = \App\Models\User::create([
        'name' => $testData['name'],
        'email' => $testData['email'],
        'password' => bcrypt($testData['password']),
        'role' => 'karenderia_owner',
        'verified' => false
    ]);
    
    echo "✅ User created: {$user->name} (ID: {$user->id})\n";
    
    // Create karenderia with the fixed method
    $karenderia = $user->karenderia()->create([
        'name' => $testData['business_name'], // Use business name as the primary name
        'business_name' => $testData['business_name'],
        'description' => $testData['description'],
        'address' => $testData['address'],
        'city' => $testData['city'],
        'province' => $testData['province'],
        'phone' => $testData['phone'],
        'business_email' => $testData['business_email'],
        'opening_time' => '09:00',
        'closing_time' => '21:00',
        'operating_days' => json_encode([]),
        'delivery_fee' => $testData['delivery_fee'],
        'delivery_time_minutes' => $testData['delivery_time_minutes'],
        'accepts_cash' => $testData['accepts_cash'],
        'accepts_online_payment' => $testData['accepts_online_payment'],
        'status' => 'pending',
        'approved_at' => null,
        'approved_by' => null,
        'average_rating' => 0.0,  // Fixed: Added missing field
        'total_reviews' => 0      // Fixed: Added missing field
    ]);
    
    echo "✅ Karenderia created: {$karenderia->business_name} (ID: {$karenderia->id})\n";
    echo "✅ Registration fix successful!\n\n";
    
    echo "Test credentials:\n";
    echo "Email: {$testData['email']}\n";
    echo "Password: {$testData['password']}\n";
    echo "Business Name: {$karenderia->business_name}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Fix failed!\n";
}

?>