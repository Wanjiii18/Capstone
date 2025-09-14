<?php
echo "🗺️ Testing Active Karenderias in Map\n";
echo "===================================\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check all karenderias by status
    echo "📊 Karenderia Status Summary:\n";
    $statuses = ['pending', 'active', 'rejected'];
    
    foreach ($statuses as $status) {
        $count = \App\Models\Karenderia::where('status', $status)->count();
        $emoji = $status === 'active' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
        echo "  $emoji $status: $count karenderias\n";
    }
    
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "🏪 ACTIVE Karenderias (Will appear in map):\n\n";
    
    $activeKarenderias = \App\Models\Karenderia::where('status', 'active')
        ->with('owner')
        ->get();
    
    if ($activeKarenderias->count() > 0) {
        foreach ($activeKarenderias as $kar) {
            echo "✅ {$kar->name}\n";
            echo "   📍 {$kar->address}\n";
            echo "   👤 Owner: {$kar->owner->name}\n";
            echo "   📧 Email: {$kar->owner->email}\n";
            echo "   📅 Approved: {$kar->approved_at}\n";
            echo "   🗺️ Coordinates: {$kar->latitude}, {$kar->longitude}\n\n";
        }
        
        echo "🎯 These karenderias will now appear in your app's nearby search!\n\n";
        
        // Test the nearby API
        echo str_repeat("=", 50) . "\n";
        echo "🌐 Testing Nearby API:\n\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/karenderias/nearby?latitude=10.3157&longitude=123.8854&radius=50000');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data && $data['success']) {
                echo "📊 API Response: {$data['message']}\n";
                echo "📍 Search results:\n";
                
                foreach ($data['data'] as $kar) {
                    echo "  🏪 {$kar['name']}\n";
                    echo "     📍 {$kar['address']}\n";
                    echo "     📏 {$kar['distance']}m away\n\n";
                }
            } else {
                echo "❌ API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "❌ No response from API\n";
        }
        
    } else {
        echo "⚠️ No active karenderias found.\n";
        echo "💡 Make sure to approve applications in the admin dashboard!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}