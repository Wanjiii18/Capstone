<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Frontend-Backend Integration ===\n\n";

// Test the API endpoint with a real HTTP request
$baseUrl = 'http://localhost:8000/api';

// Test 1: Health check
echo "🩺 Testing health endpoint...\n";
$healthResponse = file_get_contents($baseUrl . '/health');
echo "   Response: " . substr($healthResponse, 0, 100) . "...\n\n";

// Test 2: Login with owner@kaplato.com
echo "🔐 Testing login endpoint...\n";
$loginData = json_encode([
    'email' => 'owner@kaplato.com',
    'password' => 'owner123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $loginData
    ]
]);

$loginResponse = file_get_contents($baseUrl . '/auth/login', false, $context);
$loginData = json_decode($loginResponse, true);

if (isset($loginData['access_token'])) {
    echo "   ✅ Login successful\n";
    $token = $loginData['access_token'];
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 3: Get karenderia with token
    echo "🏪 Testing my-karenderia endpoint...\n";
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\n"
        ]
    ]);
    
    $karenderiaResponse = file_get_contents($baseUrl . '/karenderias/my-karenderia', false, $context);
    $karenderiaData = json_decode($karenderiaResponse, true);
    
    if (isset($karenderiaData['success']) && $karenderiaData['success']) {
        echo "   ✅ Karenderia data retrieved successfully\n";
        echo "   Name: " . $karenderiaData['data']['name'] . "\n";
        echo "   Business: " . ($karenderiaData['data']['business_name'] ?? 'N/A') . "\n";
    } else {
        echo "   ❌ Failed to get karenderia data\n";
        echo "   Response: " . substr($karenderiaResponse, 0, 200) . "\n";
    }
} else {
    echo "   ❌ Login failed\n";
    echo "   Response: " . substr($loginResponse, 0, 200) . "\n";
}
