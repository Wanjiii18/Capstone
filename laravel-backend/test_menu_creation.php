<?php

require_once 'vendor/autoload.php';

// Test script to debug menu item creation
echo "Testing Menu Item Creation...\n";

// Test data
$testData = [
    'name' => 'Test Kare-Kare',
    'description' => 'Traditional Filipino stew',
    'price' => 250.00,
    'category' => 'Main Dish',
    'ingredients' => ['Oxtail', 'Peanut Butter', 'Vegetables', 'Shrimp Paste'],
    'allergens' => ['Peanuts', 'Shellfish'],
    'calories' => 450,
    'spice_level' => 2
];

// Test API endpoint
$url = 'http://127.0.0.1:8000/api/menu-items';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 201) {
    echo "✅ Menu item created successfully!\n";
} else {
    echo "❌ Failed to create menu item\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['errors'])) {
        echo "Validation Errors:\n";
        print_r($responseData['errors']);
    }
    if (isset($responseData['message'])) {
        echo "Error Message: " . $responseData['message'] . "\n";
    }
}
