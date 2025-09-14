<?php
echo "🔍 Testing Registration API with Error Details\n";
echo "============================================\n\n";

$testData = [
    'name' => 'Maria Santos',
    'email' => 'maria.santos@email.com', // Different email to avoid conflicts
    'phone_number' => '09123456789',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    
    // Karenderia details
    'karenderia_name' => 'Tita Maria\'s Kitchen',
    'business_name' => 'Maria\'s Food Services',
    'description' => 'Authentic Filipino dishes cooked fresh daily.',
    'address' => '456 Sampaguita Street, Kamputhaw, Cebu City',
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'latitude' => 10.3156,
    'longitude' => 123.8856,
    'business_phone' => '09987654321',
    'business_email' => 'maria.kitchen@email.com',
    'opening_time' => '07:00',
    'closing_time' => '19:00',
    'operating_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
    'business_permit' => 'BP-2024-005678',
    'delivery_fee' => 25.00,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

echo "📤 Sending request to: http://127.0.0.1:8000/api/karenderia-owner/register\n\n";

// Use cURL for better error handling
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
$error = curl_error($ch);
curl_close($ch);

echo "📊 Response Details:\n";
echo "• HTTP Code: $httpCode\n";

if ($error) {
    echo "• cURL Error: $error\n";
}

if ($response) {
    echo "• Response: $response\n\n";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "📋 Parsed Response:\n";
        echo "• Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
        echo "• Message: " . ($data['message'] ?? 'No message') . "\n";
        
        if (isset($data['errors'])) {
            echo "• Validation Errors:\n";
            foreach ($data['errors'] as $field => $errors) {
                echo "  - $field: " . implode(', ', $errors) . "\n";
            }
        }
        
        if (isset($data['data'])) {
            echo "• Data: " . json_encode($data['data'], JSON_PRETTY_PRINT) . "\n";
        }
    }
} else {
    echo "• No response received\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Testing direct controller method...\n\n";

// Test direct controller method
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $controller = new \App\Http\Controllers\Auth\KarenderiaOwnerController();
    $request = new \Illuminate\Http\Request($testData);
    
    echo "🎯 Calling register method directly...\n";
    $result = $controller->register($request);
    
    echo "✅ Direct call successful!\n";
    echo "Response: " . $result->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "❌ Direct call failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}