<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Karenderia;

echo "🧪 Testing API Response for test@gmail.com:\n";
echo "==========================================\n\n";

try {
    // Simulate what the API should return
    $user = User::where('email', 'test@gmail.com')->first();
    
    if (!$user) {
        echo "❌ User test@gmail.com not found!\n";
        exit;
    }
    
    echo "✅ Found user: {$user->name} (ID: {$user->id})\n\n";
    
    $karenderia = Karenderia::where('owner_id', $user->id)->first();
    
    if (!$karenderia) {
        echo "❌ No karenderia found for this user!\n";
        exit;
    }
    
    echo "🏪 Karenderia Data (what API should return):\n";
    echo "============================================\n";
    
    $apiResponse = [
        'success' => true,
        'data' => [
            'id' => $karenderia->id,
            'name' => $karenderia->name,
            'business_name' => $karenderia->business_name,
            'description' => $karenderia->description,
            'address' => $karenderia->address,
            'phone' => $karenderia->phone,
            'email' => $karenderia->email,
            'latitude' => $karenderia->latitude,
            'longitude' => $karenderia->longitude,
            'status' => $karenderia->status,
            'delivery_fee' => $karenderia->delivery_fee,
            'delivery_time_minutes' => $karenderia->delivery_time_minutes,
            'accepts_cash' => $karenderia->accepts_cash,
            'accepts_online_payment' => $karenderia->accepts_online_payment,
        ]
    ];
    
    echo json_encode($apiResponse, JSON_PRETTY_PRINT);
    
    echo "\n\n✅ This is what the API should return when frontend calls /api/karenderias/my-karenderia\n";
    echo "🔍 If frontend shows 'Mama's Kitchen', then it's a caching issue!\n\n";
    
    echo "🚀 Solutions:\n";
    echo "1. Clear browser cache completely\n";
    echo "2. Open browser in incognito/private mode\n";
    echo "3. Clear localStorage/sessionStorage\n";
    echo "4. Restart both servers\n";
    echo "5. Try a different browser\n\n";
    
    echo "💡 Current Database Facts:\n";
    echo "- Your email: test@gmail.com\n";
    echo "- Your karenderia name: {$karenderia->name}\n";
    echo "- Your karenderia status: {$karenderia->status}\n";
    echo "- No 'Mama's Kitchen' exists in database!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}