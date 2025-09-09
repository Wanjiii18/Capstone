<?php

echo "🔍 Testing Menu Items API with User Allergens...\n\n";

$url = 'http://localhost:8000/api/menu-items/search?karenderia=1&user_id=13';

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

$data = json_decode($response, true);

echo "📊 Response Analysis:\n";
echo "- Success: " . ($data['success'] ? 'true' : 'false') . "\n";
echo "- Menu items: " . count($data['data']) . "\n";
echo "- User allergens checked: " . ($data['userAllergensChecked'] ? 'true' : 'false') . "\n";
echo "- User allergens: " . implode(', ', $data['userAllergens']) . "\n\n";

echo "🍽️ Menu Items with Allergy Status:\n";
foreach ($data['data'] as $item) {
    $status = $item['hasDangerousAllergens'] ? '⚠️ DANGER' : '✅ Safe';
    echo "- {$item['name']} - {$status} - ₱{$item['price']}\n";
    
    if ($item['hasDangerousAllergens']) {
        echo "  📢 {$item['allergyMessage']}\n";
    }
}

echo "\n✅ API test completed!\n";
?>
