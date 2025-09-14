<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating karenderia for test2@kaplato.com user...\n\n";

try {
    // Find the user
    $user = DB::table('users')->where('email', 'test2@kaplato.com')->first();
    if (!$user) {
        echo "❌ User test2@kaplato.com not found!\n";
        exit;
    }

    echo "✅ User Found: {$user->name} (ID: {$user->id})\n";

    // Check if karenderia already exists
    $existingKarenderia = DB::table('karenderias')->where('owner_id', $user->id)->first();
    if ($existingKarenderia) {
        echo "✅ Karenderia already exists!\n";
        echo "Business Name: " . ($existingKarenderia->business_name ?? 'NULL') . "\n";
        echo "Name: {$existingKarenderia->name}\n";
        exit;
    }

    // Create karenderia for the user
    $karenderiaId = DB::table('karenderias')->insertGetId([
        'owner_id' => $user->id,
        'name' => $user->name . "'s Restaurant", // account name field
        'business_name' => "Test2's Kitchen Business", // this is what should display!
        'description' => 'A new karenderia business registered through the app',
        'address' => 'Cebu City, Philippines',
        'phone' => '+639123456789',
        'email' => $user->email,
        'latitude' => 10.3157,
        'longitude' => 123.8854,
        'opening_time' => '09:00:00',
        'closing_time' => '21:00:00',
        'operating_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
        'delivery_fee' => 25.00,
        'delivery_time_minutes' => 30,
        'accepts_cash' => true,
        'accepts_online_payment' => false,
        'status' => 'active', // Make it active so it shows up
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Created karenderia (ID: {$karenderiaId})\n";
    echo "Business Name: Test2's Kitchen Business\n";
    echo "Account Name: {$user->name}'s Restaurant\n";
    echo "Status: active\n\n";

    // Verify what the API would return
    $karenderia = DB::table('karenderias')->where('id', $karenderiaId)->first();
    echo "🔍 API Response Simulation:\n";
    echo "name: {$karenderia->name}\n";
    echo "business_name: {$karenderia->business_name}\n";
    echo "Frontend Display: " . ($karenderia->business_name ?: $karenderia->name) . "\n\n";
    
    echo "🎉 Now test2@kaplato.com should see 'Test2's Kitchen Business' instead of any fallback name!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>