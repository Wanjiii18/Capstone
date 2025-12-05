<?php
/**
 * Test karenderia owner registration with detailed validation errors
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

echo "\n========================================\n";
echo "Testing Karenderia Owner Registration\n";
echo "========================================\n\n";

// Simulate the data that might be sent from the mobile app
$testData = [
    'name' => 'Test Owner',
    'email' => 'testowner' . time() . '@test.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => 'My Karenderia',
    'description' => 'A wonderful karenderia serving delicious Filipino food',
    'address' => '123 Test Street, Barangay Test',
    'city' => 'Manila',
    'province' => 'Metro Manila',
    'phone' => '09123456789',
    'business_email' => 'business@test.com',
    'opening_time' => '08:00',
    'closing_time' => '20:00'
];

echo "Test Data:\n";
foreach ($testData as $key => $value) {
    echo "  $key: $value\n";
}
echo "\n";

// Apply the same validation as the controller
$validator = Validator::make($testData, [
    // User account validation
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
    'password_confirmation' => 'required|same:password',
    
    // Business information validation
    'business_name' => 'required|string|max:255',
    'description' => 'required|string|min:10',
    'address' => 'required|string|min:10',
    'city' => 'required|string|max:100',
    'province' => 'required|string|max:100',
    
    // Location coordinates (optional - admin will set these)
    'latitude' => 'nullable|numeric|between:-90,90',
    'longitude' => 'nullable|numeric|between:-180,180',
    
    // Optional business fields
    'phone' => 'nullable|string|max:20',
    'business_email' => 'nullable|email|max:255',
    'opening_time' => 'nullable|string',
    'closing_time' => 'nullable|string',
    'operating_days' => 'nullable|array',
    'delivery_fee' => 'nullable|numeric|min:0',
    'delivery_time_minutes' => 'nullable|integer|min:0',
    'accepts_cash' => 'boolean',
    'accepts_online_payment' => 'boolean'
]);

if ($validator->fails()) {
    echo "❌ VALIDATION FAILED!\n\n";
    echo "Errors:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  • $error\n";
    }
    echo "\n";
    echo "Detailed Errors:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "✅ VALIDATION PASSED!\n";
    echo "All required fields are present and valid.\n";
}

echo "\n========================================\n";
echo "Common Issues:\n";
echo "========================================\n";
echo "1. Missing required fields:\n";
echo "   - name, email, password, password_confirmation\n";
echo "   - business_name, description, address, city, province\n\n";
echo "2. Description must be at least 10 characters\n";
echo "3. Address must be at least 10 characters\n";
echo "4. Password must be at least 8 characters\n";
echo "5. password_confirmation must match password\n";
echo "6. Email must be unique (not already registered)\n";
echo "========================================\n\n";
