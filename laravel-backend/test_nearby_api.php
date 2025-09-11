<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing the /karenderias/nearby API endpoint...\n\n";

// Test coordinates near Talisay City (South Town Center area)
$testLat = 10.2442;
$testLng = 123.8492;
$testRadius = 5000; // 5km radius

echo "📍 Test Location: {$testLat}, {$testLng}\n";
echo "📏 Radius: {$testRadius}m\n\n";

// Simulate the API call by directly calling the controller method
use App\Http\Controllers\KarenderiaController;
use Illuminate\Http\Request;

$controller = new KarenderiaController();

// Create a mock request
$request = Request::create('/api/karenderias/nearby', 'GET', [
    'latitude' => $testLat,
    'longitude' => $testLng,
    'radius' => $testRadius
]);

try {
    $response = $controller->nearby($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ API Test SUCCESS!\n";
        echo "📊 Found " . count($data['data']) . " karenderias\n\n";
        
        foreach ($data['data'] as $karenderia) {
            echo "🏪 {$karenderia['name']}\n";
            echo "   📍 {$karenderia['address']}\n";
            echo "   📏 Distance: " . round($karenderia['distance']) . "m\n";
            echo "   🌟 Rating: {$karenderia['rating']}/5.0\n";
            echo "   🍽️  Menu Items: {$karenderia['menu_items_count']}\n";
            echo "   📱 Phone: {$karenderia['phone']}\n\n";
        }
        
        echo "🎉 The nearby API is working correctly!\n";
        echo "📱 Your frontend should now be able to fetch Talisay karenderias.\n\n";
        
    } else {
        echo "❌ API Test FAILED:\n";
        echo "Error: " . $data['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
}

echo "🔧 Next Steps:\n";
echo "1. Restart your Laravel server if needed\n";
echo "2. Test the map-view in your KaPlato app\n";
echo "3. Check if Talisay karenderias appear on the map\n";
?>
