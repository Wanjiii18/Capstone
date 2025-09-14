<?php
echo "🗺️ Testing Nearby API with New Karenderia\n";
echo "=========================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test search near Cebu City
    $latitude = 10.3157;
    $longitude = 123.8854;
    $radius = 10000; // 10km radius
    
    echo "📍 Search center: $latitude, $longitude\n";
    echo "📏 Search radius: {$radius}m\n\n";
    
    // Get all active karenderias
    $karenderias = \App\Models\Karenderia::where('status', 'active')
        ->get()
        ->map(function ($karenderia) use ($latitude, $longitude) {
            // Calculate distance using Haversine formula
            $earthRadius = 6371000; // Earth's radius in meters

            $dLat = deg2rad($karenderia->latitude - $latitude);
            $dLng = deg2rad($karenderia->longitude - $longitude);

            $a = sin($dLat/2) * sin($dLat/2) +
                cos(deg2rad($latitude)) * cos(deg2rad($karenderia->latitude)) *
                sin($dLng/2) * sin($dLng/2);

            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $earthRadius * $c;

            $karenderia->distance = round($distance);
            return $karenderia;
        })
        ->filter(function ($karenderia) use ($radius) {
            return $karenderia->distance <= $radius;
        })
        ->sortBy('distance');

    echo "🏪 ACTIVE Karenderias found: {$karenderias->count()}\n";
    foreach ($karenderias as $kar) {
        echo "  • {$kar->name}\n";
        echo "    📍 {$kar->address}\n";
        echo "    📏 Distance: {$kar->distance}m\n";
        echo "    ⭐ Status: {$kar->status}\n\n";
    }
    
    // Also check pending karenderias
    echo str_repeat("-", 40) . "\n";
    $pendingKarenderias = \App\Models\Karenderia::where('status', 'pending')->get();
    echo "⏳ PENDING Karenderias: {$pendingKarenderias->count()}\n";
    foreach ($pendingKarenderias as $kar) {
        echo "  • {$kar->name} (ID: {$kar->id})\n";
        echo "    📍 {$kar->address}\n";
        echo "    👤 Owner: " . ($kar->owner ? $kar->owner->name : 'Unknown') . "\n";
        echo "    📧 Email: " . ($kar->owner ? $kar->owner->email : 'Unknown') . "\n";
        echo "    📅 Submitted: {$kar->created_at}\n\n";
    }
    
    echo "💡 Note: Only ACTIVE karenderias appear in nearby search.\n";
    echo "💡 PENDING karenderias need admin approval first.\n\n";
    
    // Test the HTTP API too
    echo str_repeat("=", 50) . "\n";
    echo "🌐 Testing HTTP API endpoint...\n\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/api/karenderias/nearby?latitude=$latitude&longitude=$longitude&radius=$radius");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "📊 HTTP Response:\n";
    echo "• Status Code: $httpCode\n";
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "• Success: ✅ YES\n";
            echo "• Message: {$data['message']}\n";
            echo "• Found: {$data['meta']['total_found']} karenderias\n\n";
            
            foreach ($data['data'] as $kar) {
                echo "🏪 {$kar['name']}\n";
                echo "  📍 {$kar['address']}\n";
                echo "  📏 {$kar['distance']}m away\n\n";
            }
        } else {
            echo "• Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "• No response received\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}