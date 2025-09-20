<?php
// Create the test users that are missing

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Karenderia;

echo "🔧 CREATING MISSING TEST USERS\n";
echo "==============================\n\n";

try {
    // First, let's see what users DO exist
    echo "👥 Current users in database:\n";
    $allUsers = User::all();
    foreach ($allUsers as $user) {
        echo "   - {$user->email} ({$user->name}) - Role: {$user->role}\n";
    }
    echo "\n";

    // Create test@gmail.com if it doesn't exist
    $testUser = User::where('email', 'test@gmail.com')->first();
    if (!$testUser) {
        echo "🔨 Creating test@gmail.com user...\n";
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password'), // Default password
            'role' => 'karenderia_owner',
            'email_verified_at' => now()
        ]);
        echo "✅ Created user: {$testUser->email}\n";
    } else {
        echo "✅ test@gmail.com already exists\n";
    }

    // Create karenderia for test@gmail.com
    $testKarenderia = Karenderia::where('owner_id', $testUser->id)->first();
    if (!$testKarenderia) {
        echo "🏪 Creating karenderia for test@gmail.com...\n";
        $testKarenderia = Karenderia::create([
            'name' => 'Test Food Restaurant',
            'business_name' => 'Test Food Business',
            'description' => 'Real test karenderia for testing',
            'address' => 'Test Address, Cebu City',
            'city' => 'Cebu City',
            'province' => 'Cebu',
            'phone' => '+639123456789',
            'email' => 'testfood@gmail.com',
            'owner_id' => $testUser->id,
            'status' => 'active',
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => true
        ]);
        echo "✅ Created karenderia: {$testKarenderia->name}\n";
    } else {
        echo "✅ Karenderia already exists for test@gmail.com\n";
    }

    // Create last@gmail.com if it doesn't exist
    $lastUser = User::where('email', 'last@gmail.com')->first();
    if (!$lastUser) {
        echo "\n🔨 Creating last@gmail.com user...\n";
        $lastUser = User::create([
            'name' => 'Last User',
            'email' => 'last@gmail.com',
            'password' => bcrypt('password'), // Default password
            'role' => 'karenderia_owner',
            'email_verified_at' => now()
        ]);
        echo "✅ Created user: {$lastUser->email}\n";
    } else {
        echo "✅ last@gmail.com already exists\n";
    }

    // Create karenderia for last@gmail.com
    $lastKarenderia = Karenderia::where('owner_id', $lastUser->id)->first();
    if (!$lastKarenderia) {
        echo "🏪 Creating karenderia for last@gmail.com...\n";
        $lastKarenderia = Karenderia::create([
            'name' => 'Last Kitchen',
            'business_name' => 'Last Kitchen Business',
            'description' => 'Another test karenderia',
            'address' => 'Last Address, Cebu City',
            'city' => 'Cebu City',
            'province' => 'Cebu',
            'phone' => '+639987654321',
            'email' => 'lastkitchen@gmail.com',
            'owner_id' => $lastUser->id,
            'status' => 'active',
            'delivery_fee' => 30.00,
            'delivery_time_minutes' => 25,
            'accepts_cash' => true,
            'accepts_online_payment' => true
        ]);
        echo "✅ Created karenderia: {$lastKarenderia->name}\n";
    } else {
        echo "✅ Karenderia already exists for last@gmail.com\n";
    }

    echo "\n🎉 SETUP COMPLETE!\n";
    echo "==================\n";
    echo "✅ test@gmail.com → Password: 'password' → Karenderia: '{$testKarenderia->name}'\n";
    echo "✅ last@gmail.com → Password: 'password' → Karenderia: '{$lastKarenderia->name}'\n";
    echo "\n💡 Now try logging in with these credentials!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}