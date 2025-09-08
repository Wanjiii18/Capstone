<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing My Karenderia API Endpoint ===\n\n";

// Test for owner@kaplato.com
$user1 = App\Models\User::where('email', 'owner@kaplato.com')->first();
if ($user1) {
    echo "🧪 Testing for owner@kaplato.com:\n";
    
    // Simulate authentication
    auth()->login($user1);
    
    $controller = new App\Http\Controllers\KarenderiaController();
    $response = $controller->getMyKarenderia();
    $data = json_decode($response->getContent(), true);
    
    echo "   Response: " . ($data['success'] ? '✅ Success' : '❌ Failed') . "\n";
    if ($data['success'] && isset($data['data'])) {
        echo "   Karenderia: {$data['data']['name']}\n";
        echo "   Business: {$data['data']['business_name']}\n";
    } else {
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    auth()->logout();
}

// Test for owner2@kaplato.com
$user2 = App\Models\User::where('email', 'owner2@kaplato.com')->first();
if ($user2) {
    echo "🧪 Testing for owner2@kaplato.com:\n";
    
    // Simulate authentication
    auth()->login($user2);
    
    $controller = new App\Http\Controllers\KarenderiaController();
    $response = $controller->getMyKarenderia();
    $data = json_decode($response->getContent(), true);
    
    echo "   Response: " . ($data['success'] ? '✅ Success' : '❌ Failed') . "\n";
    if ($data['success'] && isset($data['data'])) {
        echo "   Karenderia: {$data['data']['name']}\n";
        echo "   Business: {$data['data']['business_name']}\n";
    } else {
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    auth()->logout();
} else {
    echo "❌ owner2@kaplato.com not found\n\n";
}

// Test for owner3@kaplato.com
$user3 = App\Models\User::where('email', 'owner3@kaplato.com')->first();
if ($user3) {
    echo "🧪 Testing for owner3@kaplato.com:\n";
    
    // Simulate authentication
    auth()->login($user3);
    
    $controller = new App\Http\Controllers\KarenderiaController();
    $response = $controller->getMyKarenderia();
    $data = json_decode($response->getContent(), true);
    
    echo "   Response: " . ($data['success'] ? '✅ Success' : '❌ Failed') . "\n";
    if ($data['success'] && isset($data['data'])) {
        echo "   Karenderia: {$data['data']['name']}\n";
        echo "   Business: {$data['data']['business_name']}\n";
    } else {
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
    
    auth()->logout();
} else {
    echo "❌ owner3@kaplato.com not found\n\n";
}
