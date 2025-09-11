<?php

echo "Direct controller test for nearby method...\n";

// Include Laravel autoloader and bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\KarenderiaController;
use Illuminate\Http\Request;

// Create controller instance
$controller = new KarenderiaController();

// Test coordinates for Talisay City
$latitude = 10.244333;
$longitude = 123.849861;
$radius = 10000; // 10km

echo "Testing with coordinates: $latitude, $longitude\n";
echo "Radius: $radius meters\n\n";

// Create a mock request
$request = new Request([
    'latitude' => $latitude,
    'longitude' => $longitude,
    'radius' => $radius
]);

try {
    $response = $controller->nearby($request);
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    if ($data && isset($data['success'])) {
        if ($data['success']) {
            echo "✅ SUCCESS: " . $data['message'] . "\n";
            echo "Found " . count($data['data']) . " karenderias\n\n";
            
            foreach ($data['data'] as $i => $karenderia) {
                echo ($i + 1) . ". " . $karenderia['name'] . "\n";
                echo "   Address: " . $karenderia['address'] . "\n";
                echo "   Distance: " . round($karenderia['distance']) . "m\n";
                echo "   Rating: " . $karenderia['rating'] . "/5.0\n";
                echo "   Menu Items: " . $karenderia['menu_items_count'] . "\n\n";
            }
        } else {
            echo "❌ ERROR: " . $data['message'] . "\n";
        }
    } else {
        echo "❌ Invalid response format\n";
        echo "Raw response: $content\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTest complete.\n";
