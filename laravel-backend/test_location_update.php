<?php
// Test the location update API endpoint
$url = 'http://localhost:8000/api/karenderias/my-karenderia/location';
$token = '18|dj59Uwrsli3W67y8roWP4mYDhl2AE3BkJlsmLV8Ta009951c'; // From browser console

echo "=== TESTING LOCATION UPDATE API ===\n\n";

// Test data for Cebu City area
$testData = [
    'latitude' => 10.3157,  // Cebu City coordinates
    'longitude' => 123.8854
];

echo "Sending location update request...\n";
echo "Coordinates: {$testData['latitude']}, {$testData['longitude']}\n\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ],
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => json_encode($testData)
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "API Response:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "=== SUCCESS! ===\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
    // Now verify by calling the get API
    echo "=== VERIFYING LOCATION UPDATE ===\n";
    $getUrl = 'http://localhost:8000/api/karenderias/my-karenderia';
    
    $curl2 = curl_init();
    curl_setopt_array($curl2, [
        CURLOPT_URL => $getUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]
    ]);
    
    $verifyResponse = curl_exec($curl2);
    $verifyHttpCode = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
    curl_close($curl2);
    
    echo "Verification HTTP Code: $verifyHttpCode\n";
    
    if ($verifyHttpCode === 200) {
        $verifyData = json_decode($verifyResponse, true);
        if (isset($verifyData['data'])) {
            $karenderia = $verifyData['data'];
            echo "✅ Updated Location Verified:\n";
            echo "   Name: {$karenderia['name']}\n";
            echo "   Latitude: {$karenderia['latitude']}\n";
            echo "   Longitude: {$karenderia['longitude']}\n";
            echo "   Address: {$karenderia['address']}\n";
        }
    }
} else {
    echo "❌ Location update failed!\n";
    echo "Response: $response\n";
}
?>