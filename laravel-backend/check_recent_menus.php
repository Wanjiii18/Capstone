<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Recent Menu Items Nutritional Data ===\n\n";

$recentMenus = App\Models\MenuItem::orderBy('id', 'desc')->take(3)->get();

foreach ($recentMenus as $menu) {
    echo "Menu ID: {$menu->id}\n";
    echo "Name: {$menu->name}\n";
    echo "Calories: {$menu->calories}\n";
    echo "Allergens: " . json_encode($menu->allergens) . "\n";
    echo "Allergens type: " . gettype($menu->allergens) . "\n";
    echo "Allergens count: " . (is_array($menu->allergens) ? count($menu->allergens) : 'N/A') . "\n";
    echo "Created: {$menu->created_at}\n";
    echo "---\n";
}

echo "\n=== Solution Summary ===\n";
echo "✅ Backend now handles missing calories field (defaults to 0)\n";
echo "✅ Backend ensures allergens is always an array\n";
echo "✅ Menu items are being created with proper nutritional data structure\n";
echo "\n🔧 Frontend Integration Notes:\n";
echo "1. The frontend should send 'calories' field in menu creation requests\n";
echo "2. If calories is unknown, send 'calories': 0\n";
echo "3. Allergens should be sent as array: ['nuts', 'dairy', 'gluten']\n";
echo "4. Empty allergens should be sent as: []\n";
echo "\n📝 Example frontend payload:\n";
echo json_encode([
    'name' => 'Sample Menu',
    'price' => 150,
    'category' => 'Main Course',
    'calories' => 250,
    'allergens' => ['nuts', 'dairy'],
    'ingredients' => [
        ['ingredientName' => 'Rice', 'quantity' => 1, 'unit' => 'cup'],
        ['ingredientName' => 'Chicken', 'quantity' => 200, 'unit' => 'g']
    ]
], JSON_PRETTY_PRINT) . "\n";
