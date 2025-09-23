<?php

echo "=== TESTING LOGIN API ENDPOINT ===\n\n";

$loginData = [
    'email' => 'alica@gmail.com',
    'password' => 'password123'
];

$url = 'http://localhost:8000/api/auth/login';

echo "Testing URL: $url\n";
echo "Login data: " . json_encode($loginData) . "\n\n";

// Test with cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
    echo "\nPossible issues:\n";
    echo "1. Laravel server is not running\n";
    echo "2. Server is running on different port\n";
    echo "3. Firewall blocking connection\n";
} else {
    echo "✅ Connection successful\n";
    echo "Response: $response\n";
    
    $data = json_decode($response, true);
    if ($data) {
        if (isset($data['access_token'])) {
            echo "✅ Login successful - token received\n";
        } elseif (isset($data['message'])) {
            echo "❌ Login failed: " . $data['message'] . "\n";
        }
    }
}

echo "\n=== TROUBLESHOOTING ===\n";
echo "If you're getting HTTP failure:\n";
echo "1. Make sure Laravel server is running: php artisan serve\n";
echo "2. Check the server is on http://localhost:8000\n";
echo "3. Check browser console for exact error\n";
echo "4. Try this cURL command:\n";
echo "curl -X POST http://localhost:8000/api/auth/login \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"email\":\"alica@gmail.com\",\"password\":\"password123\"}'\n";