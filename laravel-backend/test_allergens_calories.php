<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Allergens and Calories Storage ===\n\n";

try {
    // Test creating a menu item with allergens and calories
    $menuData = [
        'name' => 'Test Menu with Allergens',
        'price' => 150.00,
        'karenderia_id' => 1,
        'category' => 'Main Course',
        'calories' => 350,
        'allergens' => ['nuts', 'dairy', 'gluten'],
        'description' => 'Test menu item with allergen and calorie data'
    ];

    echo "1. Creating menu item with allergens and calories...\n";
    $menuItem = App\Models\MenuItem::create($menuData);
    
    echo "✅ Menu item created successfully!\n";
    echo "   ID: {$menuItem->id}\n";
    echo "   Name: {$menuItem->name}\n";
    echo "   Calories: {$menuItem->calories}\n";
    echo "   Allergens: " . json_encode($menuItem->allergens) . "\n\n";

    // Test retrieving the data
    echo "2. Retrieving menu item to verify data persistence...\n";
    $retrieved = App\Models\MenuItem::find($menuItem->id);
    
    echo "✅ Data retrieved successfully!\n";
    echo "   Calories from DB: {$retrieved->calories}\n";
    echo "   Allergens from DB: " . json_encode($retrieved->allergens) . "\n";
    echo "   Allergens count: " . count($retrieved->allergens) . "\n\n";

    // Test what happens when we don't provide allergens/calories
    echo "3. Testing menu creation without allergens/calories...\n";
    $menuData2 = [
        'name' => 'Menu without allergens',
        'price' => 120.00,
        'karenderia_id' => 1,
        'category' => 'Side Dish',
        'description' => 'Menu without allergen data'
    ];

    $menuItem2 = App\Models\MenuItem::create($menuData2);
    echo "✅ Menu without allergens created!\n";
    echo "   Calories (should be null): " . ($menuItem2->calories ?? 'NULL') . "\n";
    echo "   Allergens (should be null/empty): " . json_encode($menuItem2->allergens) . "\n\n";

    // Check database schema
    echo "4. Checking database schema...\n";
    $columns = Schema::getColumnListing('menu_items');
    $hasCalories = in_array('calories', $columns);
    $hasAllergens = in_array('allergens', $columns);
    
    echo "   Calories column exists: " . ($hasCalories ? 'YES' : 'NO') . "\n";
    echo "   Allergens column exists: " . ($hasAllergens ? 'YES' : 'NO') . "\n";

    if (!$hasCalories || !$hasAllergens) {
        echo "\n❌ Missing database columns! Need to run migrations.\n";
    } else {
        echo "\n✅ Database schema is correct!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Summary ===\n";
echo "If allergens and calories are not being saved:\n";
echo "1. Check that the frontend is sending these fields\n";
echo "2. Verify the validation rules in MenuItemController\n";
echo "3. Ensure the fields are in the fillable array\n";
echo "4. Make sure the database columns exist\n";
