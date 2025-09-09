<?php

echo "🔍 Testing Menu Items API...\n\n";

// Test menu items for karenderia ID 1 (Test Karenderia)
$url = 'http://localhost:8000/api/menu-items/search?karenderia=1';

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json',
        'timeout' => 10
    ]
]);

echo "📡 Requesting: $url\n\n";

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "❌ Failed to fetch menu items\n";
    exit(1);
}

echo "✅ Raw Response:\n";
echo $response . "\n\n";

$data = json_decode($response, true);

if ($data === null) {
    echo "❌ Failed to decode JSON\n";
    exit(1);
}

echo "📊 Analysis:\n";
echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";

if (isset($data['data']) && is_array($data['data'])) {
    echo "- Menu items count: " . count($data['data']) . "\n";
    if (count($data['data']) > 0) {
        echo "- First item: " . $data['data'][0]['name'] . " - ₱" . $data['data'][0]['price'] . "\n";
    }
} else {
    echo "- No menu items data found\n";
}

echo "\n🍽️ Complete JSON:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
?>
