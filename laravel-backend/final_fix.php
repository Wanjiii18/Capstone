<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fixed = App\Models\MenuItem::whereNull('calories')->update(['calories' => 0]);
echo "Fixed {$fixed} menu items with null calories\n";

// Verify all items now have proper data
$allMenus = App\Models\MenuItem::all();
$nullCalories = $allMenus->whereNull('calories')->count();
$nullAllergens = $allMenus->whereNull('allergens')->count();

echo "Verification:\n";
echo "- Menu items with null calories: {$nullCalories}\n";
echo "- Menu items with null allergens: {$nullAllergens}\n";

if ($nullCalories == 0 && $nullAllergens == 0) {
    echo "✅ All menu items now have proper nutritional data structure!\n";
} else {
    echo "❌ Some items still need fixing\n";
}
