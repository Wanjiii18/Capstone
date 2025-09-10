<?php

echo "=== Testing API Directly ===\n";

// Test coordinates for Mandaue City, Cebu
$lat = 10.3234;
$lng = 123.9312;
$radius = 5000;

$url = "http://localhost:8000/api/karenderias/nearby?latitude=$lat&longitude=$lng&radius=$radius";

echo "URL: $url\n\n";

// Use cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response:\n";
    echo $response;
}
