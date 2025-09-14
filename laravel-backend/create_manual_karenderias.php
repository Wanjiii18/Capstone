<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Manual Karenderia Creator ===\n\n";

// You can customize these details for each karenderia you want to create
$karenderias = [
    [
        'user_name' => 'Test Kitchen Owner',
        'user_email' => 'testkitchen@kaplato.com',
        'user_password' => 'password123',
        'business_name' => 'Test Kitchen Business',
        'business_description' => 'A test kitchen specializing in Filipino cuisine',
        'business_address' => 'Cebu City, Philippines',
        'business_phone' => '+639123456789',
    ],
    [
        'user_name' => 'Maria Santos',
        'user_email' => 'maria@kitchen.com',
        'user_password' => 'password123',
        'business_name' => 'Maria\'s Home Kitchen',
        'business_description' => 'Traditional Filipino home cooking',
        'business_address' => 'Mandaue City, Cebu',
        'business_phone' => '+639987654321',
    ],
    // Add more karenderias here if you want
];

try {
    foreach ($karenderias as $index => $data) {
        echo "Creating Karenderia #" . ($index + 1) . "...\n";
        
        // Check if user already exists
        $existingUser = DB::table('users')->where('email', $data['user_email'])->first();
        if ($existingUser) {
            echo "⚠️  User {$data['user_email']} already exists, skipping...\n\n";
            continue;
        }
        
        // Create user account
        $userId = DB::table('users')->insertGetId([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => bcrypt($data['user_password']),
            'role' => 'karenderia_owner',
            'verified' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Create karenderia business record
        $karenderiaId = DB::table('karenderias')->insertGetId([
            'owner_id' => $userId,
            'name' => $data['user_name'] . "'s Restaurant", // account name
            'business_name' => $data['business_name'], // display name
            'description' => $data['business_description'],
            'address' => $data['business_address'],
            'phone' => $data['business_phone'],
            'email' => $data['user_email'],
            'latitude' => 10.3157 + (rand(-100, 100) / 10000), // Random nearby location
            'longitude' => 123.8854 + (rand(-100, 100) / 10000),
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'operating_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'delivery_fee' => 25.00,
            'delivery_time_minutes' => 30,
            'accepts_cash' => true,
            'accepts_online_payment' => false,
            'status' => 'active', // Make it active immediately
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✅ Created User: {$data['user_name']} (ID: {$userId})\n";
        echo "✅ Created Karenderia: {$data['business_name']} (ID: {$karenderiaId})\n";
        echo "📧 Login: {$data['user_email']} / {$data['user_password']}\n";
        echo "🏪 Business Name: {$data['business_name']}\n\n";
    }
    
    echo "🎉 All karenderias created successfully!\n";
    echo "\nYou can now login with any of the above credentials and see unique business names!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>