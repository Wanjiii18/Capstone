<?php

// Simple script to test API response structure
$url = 'http://localhost:8000/api/karenderias/nearby?latitude=10.3157&longitude=123.8854&radius=5000';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json',
        'timeout' => 10
    ]
]);

echo "🔍 Testing API endpoint: $url\n\n";

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch API response\n";
    exit(1);
}

echo "✅ Raw API Response:\n";
echo $response . "\n\n";

$data = json_decode($response, true);

if ($data === null) {
    echo "❌ Failed to decode JSON response\n";
    exit(1);
}

echo "📊 Response Analysis:\n";
echo "- Response Type: " . gettype($data) . "\n";
echo "- Top-level keys: " . implode(', ', array_keys($data)) . "\n";

if (isset($data['data'])) {
    echo "- 'data' property type: " . gettype($data['data']) . "\n";
    if (is_array($data['data'])) {
        echo "- 'data' array length: " . count($data['data']) . "\n";
        if (count($data['data']) > 0) {
            echo "- First item keys: " . implode(', ', array_keys($data['data'][0])) . "\n";
        }
    } else {
        echo "- 'data' property value: " . json_encode($data['data']) . "\n";
    }
} else {
    echo "- ❌ No 'data' property found!\n";
}

echo "\n🔍 Full JSON structure:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
?>
