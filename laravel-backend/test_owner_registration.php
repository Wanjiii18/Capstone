<?php
echo "🧪 Testing Karenderia Owner Registration System\n";
echo "===========================================\n\n";

// Test data for a new karenderia owner
$testData = [
    'name' => 'Juan Cruz',
    'email' => 'juan.cruz@email.com',
    'phone_number' => '09123456789',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    
    // Karenderia details
    'karenderia_name' => 'Lola Elena\'s Carinderia',
    'business_name' => 'Elena\'s Food Services',
    'description' => 'Traditional Filipino home-cooked meals served with love. We specialize in adobo, sinigang, and other Filipino comfort food.',
    'address' => '123 Mango Street, Lahug, Cebu City',
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'latitude' => 10.3452,
    'longitude' => 123.9132,
    'business_phone' => '09987654321',
    'business_email' => 'elena.carinderia@email.com',
    'opening_time' => '06:00',
    'closing_time' => '20:00',
    'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
    'business_permit' => 'BP-2024-001234',
    'delivery_fee' => 30.00,
    'delivery_time_minutes' => 25,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

echo "📝 Test Data:\n";
echo "• Owner: {$testData['name']} ({$testData['email']})\n";
echo "• Karenderia: {$testData['karenderia_name']}\n";
echo "• Location: {$testData['address']}\n";
echo "• Coordinates: {$testData['latitude']}, {$testData['longitude']}\n\n";

echo "🚀 Sending registration request...\n";

// Create context for the POST request
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        'content' => json_encode($testData)
    ]
]);

try {
    $response = file_get_contents('http://127.0.0.1:8000/api/karenderia-owner/register', false, $context);
    
    if ($response === false) {
        echo "❌ ERROR: Failed to get response\n";
        echo "HTTP Headers: " . print_r($http_response_header ?? [], true) . "\n";
    } else {
        echo "✅ SUCCESS: Got response\n";
        echo "📄 Raw Response: " . substr($response, 0, 1000) . "\n\n";
        
        $data = json_decode($response, true);
        if ($data) {
            echo "📊 Parsed Response:\n";
            echo "• Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
            echo "• Message: " . ($data['message'] ?? 'No message') . "\n";
            
            if (isset($data['data'])) {
                echo "• Owner ID: " . ($data['data']['owner']['id'] ?? 'Not set') . "\n";
                echo "• Karenderia ID: " . ($data['data']['karenderia']['id'] ?? 'Not set') . "\n";
                echo "• Status: " . ($data['data']['karenderia']['status'] ?? 'Not set') . "\n";
            }
            
            if (isset($data['errors'])) {
                echo "• Errors: " . json_encode($data['errors'], JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "⚠️ WARNING: Response is not valid JSON\n";
        }
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Now checking database for new records...\n\n";

// Test direct database check
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check for the new user
    $user = \App\Models\User::where('email', $testData['email'])->first();
    if ($user) {
        echo "✅ User created successfully:\n";
        echo "• ID: {$user->id}\n";
        echo "• Name: {$user->name}\n";
        echo "• Role: {$user->role}\n";
        echo "• Verified: " . ($user->verified ? 'Yes' : 'No') . "\n";
        echo "• Application Status: {$user->application_status}\n\n";
        
        // Check for the karenderia
        $karenderia = $user->karenderia;
        if ($karenderia) {
            echo "✅ Karenderia created successfully:\n";
            echo "• ID: {$karenderia->id}\n";
            echo "• Name: {$karenderia->name}\n";
            echo "• Status: {$karenderia->status}\n";
            echo "• Address: {$karenderia->address}\n";
            echo "• Coordinates: {$karenderia->latitude}, {$karenderia->longitude}\n";
            echo "• Operating Days: " . implode(', ', $karenderia->operating_days ?? []) . "\n";
        } else {
            echo "❌ No karenderia found for this user\n";
        }
    } else {
        echo "❌ No user found with email: {$testData['email']}\n";
    }
} catch (Exception $e) {
    echo "❌ Database check failed: " . $e->getMessage() . "\n";
}

echo "\n🎯 Test completed!\n";