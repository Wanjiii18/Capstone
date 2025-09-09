<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Create minimal Laravel app for testing
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Nearby API...\n";

try {
    // Create a test request
    $request = Request::create('/api/karenderias/nearby', 'GET', [
        'latitude' => 10.3157,
        'longitude' => 123.8854,
        'radius' => 5000
    ]);

    // Get the controller
    $controller = new \App\Http\Controllers\KarenderiaController();
    
    // Call the nearby method
    $response = $controller->nearby($request);
    
    echo "✅ Status: " . $response->getStatusCode() . "\n";
    
    $data = json_decode($response->getContent(), true);
    if (isset($data['data'])) {
        echo "✅ Karenderias found: " . count($data['data']) . "\n";
        foreach ($data['data'] as $index => $karenderia) {
            echo "   " . ($index + 1) . ". " . $karenderia['name'] . " (Distance: " . $karenderia['distance'] . "m)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
