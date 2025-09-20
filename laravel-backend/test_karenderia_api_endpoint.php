<?php

require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Karenderia API Endpoint ===\n\n";

// Test with owner@kaplato.com user
$user = \App\Models\User::where('email', 'owner@kaplato.com')->first();

if (!$user) {
    echo "❌ User owner@kaplato.com not found!\n";
    exit;
}

echo "✅ Found user: {$user->name} ({$user->email})\n";
echo "User ID: {$user->id}\n";
echo "Role: {$user->role}\n\n";

// Check their karenderia
$karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();

if (!$karenderia) {
    echo "❌ No karenderia found for this user!\n";
    
    echo "\nLet's check what's in the karenderias table:\n";
    $allKarenderias = \App\Models\Karenderia::all();
    foreach($allKarenderias as $k) {
        echo "- ID: {$k->id}, Owner ID: {$k->owner_id}, Business: {$k->business_name}\n";
    }
} else {
    echo "✅ Found karenderia:\n";
    echo "  ID: {$karenderia->id}\n";
    echo "  Name: {$karenderia->name}\n";
    echo "  Business Name: {$karenderia->business_name}\n";
    echo "  Status: {$karenderia->status}\n";
    echo "  Owner ID: {$karenderia->owner_id}\n";
    echo "  Address: {$karenderia->address}\n";
    
    // Test API response format
    echo "\n=== API Response Format ===\n";
    $apiResponse = [
        'success' => true,
        'data' => [
            'id' => $karenderia->id,
            'name' => $karenderia->name,
            'business_name' => $karenderia->business_name,
            'description' => $karenderia->description,
            'address' => $karenderia->address,
            'city' => $karenderia->city,
            'province' => $karenderia->province,
            'phone' => $karenderia->phone,
            'business_email' => $karenderia->business_email,
            'status' => $karenderia->status,
            'owner_id' => $karenderia->owner_id,
        ]
    ];
    
    echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n";
}

// Also test with alica user
echo "\n" . str_repeat("=", 50) . "\n";
echo "Testing with alica@kaplato.com user:\n\n";

$alicaUser = \App\Models\User::where('email', 'alica@kaplato.com')->first();
if ($alicaUser) {
    echo "✅ Found user: {$alicaUser->name} ({$alicaUser->email})\n";
    
    $alicaKarenderia = \App\Models\Karenderia::where('owner_id', $alicaUser->id)->first();
    if ($alicaKarenderia) {
        echo "✅ Business: {$alicaKarenderia->business_name}\n";
        echo "  Status: {$alicaKarenderia->status}\n";
    } else {
        echo "❌ No karenderia found for alica\n";
    }
}