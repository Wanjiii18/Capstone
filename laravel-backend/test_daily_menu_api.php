<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

echo "=== Daily Menu API Test ===\n\n";

// Find the user
$user = User::where('email', 'burgos123@gmail.com')->first();
if (!$user) {
    echo "❌ User not found\n";
    exit;
}

echo "✅ Found user: {$user->email} (ID: {$user->id})\n";

// Create a token for API testing
$token = $user->createToken('test-daily-menu')->plainTextToken;
echo "✅ Generated API token: " . substr($token, 0, 20) . "...\n";

// Test the endpoints with curl
$baseUrl = 'http://192.168.1.17:8000';
$headers = [
    "Authorization: Bearer $token",
    "Content-Type: application/json",
    "Accept: application/json"
];

echo "\n🧪 Testing Daily Menu Endpoints:\n";

// Test 1: Get available menu items
echo "\n1. Testing GET /api/daily-menu/available-items\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/api/daily-menu/available-items");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "   ✅ Success! Found " . count($data['data']) . " menu items\n";
    foreach ($data['data'] as $item) {
        echo "      - {$item['name']} (₱{$item['price']})\n";
    }
} else {
    echo "   ❌ Failed: $response\n";
}

// Test 2: Get daily menu (should be empty initially)
echo "\n2. Testing GET /api/daily-menu\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/api/daily-menu");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "   ✅ Success! Daily menu has " . count($data['data']) . " items for today\n";
} else {
    echo "   ❌ Failed: $response\n";
}

echo "\n🎉 Daily Menu API is now ready!\n";
echo "📱 To test in the mobile app:\n";
echo "   1. Log in with: burgos123@gmail.com\n";
echo "   2. Navigate to Daily Menu Management\n";
echo "   3. You should now see the available menu items without 403 errors\n";