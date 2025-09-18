<?php

require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Karenderia API ===\n\n";

// Check users
echo "1. Checking users:\n";
$users = \App\Models\User::whereIn('email', ['owner@kaplato.com', 'rosa.karenderia@email.com'])->get();

foreach($users as $user) {
    echo "User: {$user->email} (ID: {$user->id}, Role: {$user->role})\n";
    
    // Check their karenderia
    $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
    if($karenderia) {
        echo "  -> Karenderia: {$karenderia->name} (ID: {$karenderia->id})\n";
        echo "  -> Business Name: " . ($karenderia->business_name ?? 'Not set') . "\n";
        echo "  -> Status: {$karenderia->status}\n";
    } else {
        echo "  -> No karenderia found\n";
    }
    echo "\n";
}

// Test the API endpoint logic directly
echo "2. Testing API endpoint logic:\n";
foreach($users as $user) {
    echo "Testing for user: {$user->email}\n";
    
    // Simulate the controller logic
    $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
    
    if (!$karenderia) {
        echo "  -> No karenderia found for this user\n";
    } else {
        echo "  -> API would return:\n";
        echo "    - id: {$karenderia->id}\n";
        echo "    - name: {$karenderia->name}\n";
        echo "    - business_name: " . ($karenderia->business_name ?? $karenderia->name) . "\n";
        echo "    - description: {$karenderia->description}\n";
        echo "    - status: {$karenderia->status}\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";