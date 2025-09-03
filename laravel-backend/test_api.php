<?php

// Test menu item creation API with complex ingredients
echo "Testing menu item creation API with complex ingredients...\n";

$testData = [
    'name' => 'Test Sisig',
    'description' => 'Traditional Filipino sisig',
    'price' => 180.00,
    'category' => 'Main Dish',
    'ingredients' => [
        [
            'ingredientId' => 'temp_123_abc',
            'ingredientName' => 'Pork Belly',
            'quantity' => 400,
            'unit' => 'g',
            'cost' => 200
        ],
        [
            'ingredientId' => 'temp_456_def', 
            'ingredientName' => 'Pork Liver',
            'quantity' => 100,
            'unit' => 'g',
            'cost' => 80
        ]
    ],
    'allergens' => [],
    'is_available' => true,
    'preparation_time' => 15
];

echo "Test data:\n";
print_r($testData);

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
$curlError = curl_error($curl);
curl_close($curl);

echo "\nAPI Response:\n";
echo "HTTP Code: $httpCode\n";

if ($curlError) {
    echo "CURL Error: $curlError\n";
}

if ($response) {
    echo "Response: $response\n";
    
    $responseData = json_decode($response, true);
    if (isset($responseData['errors'])) {
        echo "\nValidation Errors:\n";
        foreach ($responseData['errors'] as $field => $errors) {
            echo "  $field: " . implode(', ', $errors) . "\n";
        }
    }
} else {
    echo "No response received\n";
}
