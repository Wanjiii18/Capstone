<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Frontend Data Format ===\n\n";

// Simulate exactly what the frontend sends
$frontendData = [
    'name' => 'Test Username',  // This comes from registerData.username
    'email' => 'frontend_test_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => 'Test Business Name',  // This comes from registerData.businessName
    'address' => 'Test Business Address',     // This comes from registerData.businessAddress
    'phone' => '+639123456789',               // This comes from registerData.businessPhone
    'description' => 'Test Business Description', // This comes from registerData.businessDescription
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'delivery_fee' => 25,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

echo "Frontend data being sent:\n";
echo json_encode($frontendData, JSON_PRETTY_PRINT) . "\n\n";

// Test with possible edge cases that might cause 422
$testCases = [
    'Valid Data' => $frontendData,
    'Short Description' => array_merge($frontendData, ['description' => 'Short']), // Less than 10 chars
    'Short Address' => array_merge($frontendData, ['address' => 'Short']), // Less than 10 chars
    'Missing Business Name' => array_merge($frontendData, ['business_name' => '']),
    'Invalid Email' => array_merge($frontendData, ['email' => 'invalid-email']),
    'Password Mismatch' => array_merge($frontendData, ['password_confirmation' => 'different']),
];

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

foreach ($testCases as $testName => $testData) {
    echo "=== Testing: $testName ===\n";
    
    // Use unique email for each test
    $testData['email'] = str_replace('@', '_' . time() . '_' . rand(1000, 9999) . '@', $testData['email']);
    
    $controller = new AuthController();
    $request = new Request($testData);
    
    try {
        $response = $controller->registerKarenderiaOwner($request);
        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getContent(), true);
        
        if ($statusCode === 422) {
            echo "❌ 422 Validation Error:\n";
            if (isset($responseData['errors'])) {
                foreach ($responseData['errors'] as $field => $errors) {
                    echo "  $field: " . implode(', ', $errors) . "\n";
                }
            }
        } else if ($statusCode === 200 || $statusCode === 201) {
            echo "✅ Success (Status: $statusCode)\n";
        } else {
            echo "❓ Status: $statusCode\n";
            echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Test what happens when frontend sends empty or missing business fields
echo "=== Testing Empty Business Fields (Common Frontend Issue) ===\n";

$emptyBusinessData = [
    'name' => 'Test User',
    'email' => 'empty_test_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => '',  // Empty
    'address' => '',        // Empty
    'phone' => '',          // Empty
    'description' => '',    // Empty
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'delivery_fee' => 25,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

$controller = new AuthController();
$request = new Request($emptyBusinessData);

try {
    $response = $controller->registerKarenderiaOwner($request);
    $statusCode = $response->getStatusCode();
    $responseData = json_decode($response->getContent(), true);
    
    echo "Status: $statusCode\n";
    if ($statusCode === 422 && isset($responseData['errors'])) {
        echo "Validation errors for empty fields:\n";
        foreach ($responseData['errors'] as $field => $errors) {
            echo "  $field: " . implode(', ', $errors) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

?>