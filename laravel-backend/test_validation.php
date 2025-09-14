<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Karenderia Registration Validation ===\n\n";

// Simulate the exact data the frontend would send
$testData = [
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => 'password123', // 11 characters - should pass min:8
    'password_confirmation' => 'password123',
    'business_name' => 'Test Kitchen',
    'address' => '123 Test Street, Cebu City',
    'phone' => '+639123456789',
    'description' => 'A test kitchen for validation', // 30+ characters - should pass min:10
    'city' => 'Cebu City',
    'province' => 'Cebu',
    'delivery_fee' => 25,
    'delivery_time_minutes' => 30,
    'accepts_cash' => true,
    'accepts_online_payment' => false
];

echo "Testing with data:\n";
foreach ($testData as $key => $value) {
    if (is_bool($value)) {
        $value = $value ? 'true' : 'false';
    }
    echo "  {$key}: {$value}\n";
}
echo "\n";

// Test validation rules manually
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
    echo "❌ VALIDATION FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  • {$error}\n";
    }
} else {
    echo "✅ VALIDATION PASSED!\n";
    echo "The validation rules are working correctly.\n";
    echo "\n🔍 Check if this specific email already exists in database:\n";
    
    $existingUser = DB::table('users')->where('email', $testData['email'])->first();
    if ($existingUser) {
        echo "❌ Email {$testData['email']} already exists in database!\n";
        echo "This would cause the unique:users validation to fail.\n";
    } else {
        echo "✅ Email is unique - no conflicts.\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "💡 COMMON ISSUES TO CHECK:\n";
echo "1. Password less than 8 characters\n";
echo "2. Description less than 10 characters\n";
echo "3. Email already exists in database\n";
echo "4. Missing required fields (city, province)\n";
echo "5. Address less than 10 characters\n";

?>