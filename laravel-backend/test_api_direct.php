<?php
// Simple API test without Laravel bootstrap
$url = 'http://localhost:8000/api/karenderias/my-karenderia';
$token = '18|dj59Uwrsli3W67y8roWP4mYDhl2AE3BkJlsmLV8Ta009951c'; // From browser console

echo "=== TESTING API RESPONSE ===\n\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
echo "Raw Response:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "=== PARSED RESPONSE ===\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($data['data'])) {
        echo "=== KARENDERIA DETAILS ===\n";
        $karenderia = $data['data'];
        foreach ($karenderia as $key => $value) {
            echo "$key: $value\n";
        }
    }
}
?>
