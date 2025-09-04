<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Menu Creation Fix ===\n\n";

// Check our karenderia owner
$user = App\Models\User::where('role', 'karenderia_owner')->first();
echo "✅ Karenderia Owner: {$user->name} (ID: {$user->id})\n";
echo "   Email: {$user->email}\n";

// Check karenderia
$karenderia = App\Models\Karenderia::where('owner_id', $user->id)->first();
echo "✅ Karenderia: {$karenderia->business_name} (ID: {$karenderia->id})\n\n";

// Simulate creating a menu item (what the controller does)
echo "Testing menu item creation...\n";

$menuData = [
    'name' => 'Test Adobo',
    'description' => 'Delicious Filipino adobo',
    'price' => 150.00,
    'category' => 'Main Course',
    'preparation_time' => 20,
    'ingredients' => [
        ['name' => 'Pork', 'quantity' => '500g'],
        ['name' => 'Soy Sauce', 'quantity' => '1/2 cup'],
        ['name' => 'Vinegar', 'quantity' => '1/4 cup']
    ],
    'karenderia_id' => $karenderia->id
];

try {
    $menuItem = App\Models\MenuItem::create($menuData);
    echo "✅ Menu item created successfully!\n";
    echo "   ID: {$menuItem->id}\n";
    echo "   Name: {$menuItem->name}\n";
    echo "   Price: ₱{$menuItem->price}\n";
    echo "   Ingredients: " . json_encode($menuItem->ingredients) . "\n";
    
    // Test retrieval for customer view
    echo "\n=== Customer View Test ===\n";
    $customerMenus = App\Models\MenuItem::where('karenderia_id', $karenderia->id)->get();
    echo "Menu items available for customers: {$customerMenus->count()}\n";
    foreach ($customerMenus as $menu) {
        echo "- {$menu->name}: ₱{$menu->price}\n";
        echo "  Ingredients: " . count($menu->ingredients) . " items\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "Database counts:\n";
echo "- Users: " . App\Models\User::count() . "\n";
echo "- Karenderias: " . App\Models\Karenderia::count() . "\n";
echo "- Menu Items: " . App\Models\MenuItem::count() . "\n";
