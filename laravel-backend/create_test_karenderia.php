<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the first karenderia owner
$user = App\Models\User::where('role', 'karenderia_owner')->first();

if (!$user) {
    echo "No karenderia owner found!\n";
    exit(1);
}

// Create a test karenderia
$karenderia = App\Models\Karenderia::create([
    'owner_id' => $user->id,
    'name' => 'Test Karenderia',
    'business_name' => 'Test Karenderia Business',
    'address' => '123 Test Street',
    'city' => 'Test City',
    'province' => 'Test Province',
    'description' => 'A test karenderia for menu testing',
    'phone' => '09123456789',
    'status' => 'approved'
]);

echo "✅ Created karenderia with ID: {$karenderia->id}\n";
echo "Owner: {$user->name} (ID: {$user->id})\n";
echo "Karenderia: {$karenderia->business_name}\n";
echo "\nNow you can log in as this karenderia owner and add menu items!\n";
