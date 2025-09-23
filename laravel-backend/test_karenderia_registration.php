<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING KARENDERIA OWNER REGISTRATION ===\n\n";

// Test data for karenderia owner registration
$testData = [
    'name' => 'Test Owner ' . time(),
    'email' => 'testowner' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => 'Test Karenderia Business',
    'description' => 'A test karenderia for testing purposes. Serving delicious local food.',
    'address' => '123 Test Street, Test City',
    'city' => 'Test City',
    'province' => 'Test Province',
    'phone' => '+639123456789',
    'business_email' => 'business@test.com',
    'opening_time' => '08:00',
    'closing_time' => '20:00'
];

echo "Testing karenderia owner registration with data:\n";
echo "Name: " . $testData['name'] . "\n";
echo "Email: " . $testData['email'] . "\n";
echo "Business: " . $testData['business_name'] . "\n";
echo "Address: " . $testData['address'] . "\n\n";

// Simulate the API call
try {
    $validator = Illuminate\Support\Facades\Validator::make($testData, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'password_confirmation' => 'required|same:password',
        'business_name' => 'required|string|max:255',
        'description' => 'required|string|min:10',
        'address' => 'required|string|min:10',
        'city' => 'required|string|max:100',
        'province' => 'required|string|max:100',
    ]);

    if ($validator->fails()) {
        echo "❌ Validation failed:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "  - $error\n";
        }
        exit(1);
    }

    echo "✅ Validation passed\n\n";

    // Create user account
    $user = App\Models\User::create([
        'name' => $testData['name'],
        'email' => $testData['email'],
        'password' => Illuminate\Support\Facades\Hash::make($testData['password']),
        'role' => 'karenderia_owner',
        'verified' => false
    ]);

    echo "✅ User account created - ID: {$user->id}\n";

    // Create karenderia business record
    $karenderia = $user->karenderia()->create([
        'name' => $testData['business_name'], // Add the required name field
        'business_name' => $testData['business_name'],
        'description' => $testData['description'],
        'address' => $testData['address'],
        'city' => $testData['city'],
        'province' => $testData['province'],
        'phone' => $testData['phone'],
        'business_email' => $testData['business_email'],
        'opening_time' => $testData['opening_time'],
        'closing_time' => $testData['closing_time'],
        'operating_days' => json_encode([]),
        'delivery_fee' => 0,
        'delivery_time_minutes' => 30,
        'accepts_cash' => true,
        'accepts_online_payment' => false,
        'status' => 'pending',
        'approved_at' => null,
        'approved_by' => null
    ]);

    echo "✅ Karenderia business created - ID: {$karenderia->id}\n";
    echo "✅ Status: {$karenderia->status}\n\n";

    echo "=== VERIFICATION ===\n";
    $pendingCount = App\Models\Karenderia::where('status', 'pending')->count();
    echo "Total pending karenderias: $pendingCount\n";

    $latestKarenderia = App\Models\Karenderia::latest()->first();
    echo "Latest karenderia: {$latestKarenderia->business_name} (Status: {$latestKarenderia->status})\n";

    echo "\n✅ Registration test completed successfully!\n";
    echo "The new karenderia should now appear in the admin pending list.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}