<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing Existing Menu Items with NULL Allergens ===\n\n";

// Find menu items with null allergens
$menusWithNullAllergens = App\Models\MenuItem::whereNull('allergens')->get();
echo "Found {$menusWithNullAllergens->count()} menu items with NULL allergens\n\n";

foreach ($menusWithNullAllergens as $menu) {
    echo "Fixing Menu ID: {$menu->id} - {$menu->name}\n";
    $menu->allergens = [];
    if ($menu->calories === null) {
        $menu->calories = 0;
        echo "  Also set calories to 0\n";
    }
    $menu->save();
    echo "  ✅ Fixed allergens to empty array\n";
}

// Find menu items with null calories
$menusWithNullCalories = App\Models\MenuItem::whereNull('calories')->get();
echo "\nFound {$menusWithNullCalories->count()} menu items with NULL calories\n\n";

foreach ($menusWithNullCalories as $menu) {
    echo "Fixing Menu ID: {$menu->id} - {$menu->name}\n";
    $menu->calories = 0;
    $menu->save();
    echo "  ✅ Set calories to 0\n";
}

echo "\n=== Verification ===\n";
$allMenus = App\Models\MenuItem::all();
foreach ($allMenus as $menu) {
    $allergensStatus = is_array($menu->allergens) ? '✅ Array' : '❌ ' . gettype($menu->allergens);
    $caloriesStatus = is_numeric($menu->calories) ? '✅ ' . $menu->calories : '❌ ' . gettype($menu->calories);
    
    echo "ID {$menu->id}: Allergens {$allergensStatus}, Calories {$caloriesStatus}\n";
}

echo "\n✅ All existing menu items now have proper nutritional data structure!\n";
