<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debugging 422 Karenderia Registration Error ===\n\n";

// Test the exact data that would come from the frontend
$testData = [
    'name' => 'Debug Test User',
    'email' => 'debug_' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => 'Debug Test Restaurant',
    'description' => 'Testing the 422 error issue',
    'address' => '123 Debug Street, Debug City',
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'phone' => '+639123456789',
    'business_email' => 'debug@restaurant.com',
    'opening_time' => '09:00',
    'closing_time' => '21:00',
    'delivery_fee' => 25,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

echo "Test data being sent:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test direct controller method
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$controller = new AuthController();
$request = new Request($testData);

try {
    echo "Testing AuthController::registerKarenderiaOwner...\n";
    $response = $controller->registerKarenderiaOwner($request);
    
    $statusCode = $response->getStatusCode();
    $responseData = json_decode($response->getContent(), true);
    
    echo "Status Code: $statusCode\n";
    echo "Response:\n";
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($statusCode === 422) {
        echo "❌ 422 Validation Error Details:\n";
        if (isset($responseData['errors'])) {
            foreach ($responseData['errors'] as $field => $errors) {
                echo "  $field: " . implode(', ', $errors) . "\n";
            }
        }
        echo "\n";
    } else if ($statusCode === 200 || $statusCode === 201) {
        echo "✅ Registration successful!\n";
    } else {
        echo "❌ Unexpected status code: $statusCode\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception caught: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Also test validation rules directly
echo "=== Testing Validation Rules ===\n\n";

use Illuminate\Support\Facades\Validator;

$validationRules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
    'password_confirmation' => 'required|same:password',
    'business_name' => 'required|string|max:255',
    'description' => 'required|string|min:10',
    'address' => 'required|string|min:10',
    'city' => 'required|string|max:100',
    'province' => 'required|string|max:100',
    'latitude' => 'nullable|numeric|between:-90,90',
    'longitude' => 'nullable|numeric|between:-180,180',
    'phone' => 'nullable|string|max:20',
    'business_email' => 'nullable|email|max:255',
    'opening_time' => 'nullable|string',
    'closing_time' => 'nullable|string',
    'operating_days' => 'nullable|array',
    'delivery_fee' => 'nullable|numeric|min:0',
    'delivery_time_minutes' => 'nullable|integer|min:0',
    'accepts_cash' => 'boolean',
    'accepts_online_payment' => 'boolean'
];

$validator = Validator::make($testData, $validationRules);

if ($validator->fails()) {
    echo "❌ Direct validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "✅ Direct validation passed\n";
}

?>