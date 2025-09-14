<?php
echo "🎯 Final Registration Test - Fresh Data\n";
echo "=====================================\n\n";

$testData = [
    'name' => 'Rosa Dela Cruz',
    'email' => 'rosa.delacruz@email.com', // Fresh email
    'phone_number' => '09198765432',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    
    // Karenderia details
    'karenderia_name' => 'Rosa\'s Lutong Bahay',
    'business_name' => 'Rosa\'s Home Cooking Business',
    'description' => 'Authentic home-style Filipino cooking. Fresh ingredients, traditional recipes, and made with love.',
    'address' => '789 Rose Street, Guadalupe, Cebu City',
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'latitude' => 10.2998,
    'longitude' => 123.8997,
    'business_phone' => '09876543210',
    'business_email' => 'rosa.lutongbahay@gmail.com',
    'opening_time' => '05:30',
    'closing_time' => '21:00',
    'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
    'business_permit' => 'BP-2024-789012',
    'delivery_fee' => 20.00,
    'delivery_time_minutes' => 35,
    'accepts_cash' => true,
    'accepts_online_payment' => true
];

echo "📝 Testing with owner: {$testData['name']}\n";
echo "📧 Email: {$testData['email']}\n";
echo "🏪 Karenderia: {$testData['karenderia_name']}\n";
echo "📍 Location: {$testData['address']}\n\n";

// Use cURL for the test
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/karenderia-owner/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📊 API Response:\n";
echo "• Status Code: $httpCode\n";

if ($response) {
    $data = json_decode($response, true);
    if ($data) {
        echo "• Success: " . ($data['success'] ? '✅ YES' : '❌ NO') . "\n";
        echo "• Message: " . ($data['message'] ?? 'No message') . "\n";
        
        if (isset($data['errors'])) {
            echo "\n❌ Validation Errors:\n";
            foreach ($data['errors'] as $field => $errors) {
                echo "  • $field: " . implode(', ', $errors) . "\n";
            }
        }
        
        if (isset($data['data'])) {
            echo "\n✅ Registration Data:\n";
            if (isset($data['data']['owner'])) {
                $owner = $data['data']['owner'];
                echo "  • Owner ID: {$owner['id']}\n";
                echo "  • Owner Name: {$owner['name']}\n";
                echo "  • Application Status: {$owner['application_status']}\n";
            }
            
            if (isset($data['data']['karenderia'])) {
                $kar = $data['data']['karenderia'];
                echo "  • Karenderia ID: {$kar['id']}\n";
                echo "  • Karenderia Name: {$kar['name']}\n";
                echo "  • Status: {$kar['status']}\n";
                echo "  • Address: {$kar['address']}\n";
            }
        }
    } else {
        echo "• Raw Response: $response\n";
    }
} else {
    echo "• No response received\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Database Verification\n\n";

// Verify in database
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $user = \App\Models\User::where('email', $testData['email'])->with('karenderia')->first();
    
    if ($user) {
        echo "✅ Found user in database:\n";
        echo "  • ID: {$user->id}\n";
        echo "  • Name: {$user->name}\n";
        echo "  • Email: {$user->email}\n";
        echo "  • Role: {$user->role}\n";
        echo "  • Verified: " . ($user->verified ? 'Yes' : 'No') . "\n";
        echo "  • Application Status: {$user->application_status}\n\n";
        
        if ($user->karenderia) {
            $kar = $user->karenderia;
            echo "✅ Found karenderia in database:\n";
            echo "  • ID: {$kar->id}\n";
            echo "  • Name: {$kar->name}\n";
            echo "  • Business Name: {$kar->business_name}\n";
            echo "  • Status: {$kar->status}\n";
            echo "  • Address: {$kar->address}\n";
            echo "  • City: {$kar->city}, {$kar->province}\n";
            echo "  • Coordinates: {$kar->latitude}, {$kar->longitude}\n";
            echo "  • Operating Days: " . implode(', ', $kar->operating_days ?? []) . "\n";
            echo "  • Hours: {$kar->opening_time} - {$kar->closing_time}\n";
            echo "  • Delivery Fee: ₱{$kar->delivery_fee}\n";
            echo "  • Delivery Time: {$kar->delivery_time_minutes} minutes\n";
            echo "  • Accepts Cash: " . ($kar->accepts_cash ? 'Yes' : 'No') . "\n";
            echo "  • Accepts Online: " . ($kar->accepts_online_payment ? 'Yes' : 'No') . "\n";
        } else {
            echo "❌ No karenderia found for this user\n";
        }
    } else {
        echo "❌ User not found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test completed!\n";