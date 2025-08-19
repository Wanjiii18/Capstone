<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Karenderia;
use App\Models\Recipe;
use App\Models\MenuItem;

echo "=== Recipe Management System Test ===\n\n";

// Test 1: Check existing data
echo "1. Checking existing data...\n";
echo "   Users: " . User::count() . "\n";
echo "   Karenderias: " . Karenderia::count() . "\n";
echo "   Recipes: " . Recipe::count() . "\n";
echo "   Menu Items: " . MenuItem::count() . "\n\n";

// Test 2: Create test user and karenderia if they don't exist
echo "2. Creating test data...\n";

$owner = User::firstOrCreate([
    'email' => 'test@recipe.com'
], [
    'name' => 'Test Recipe Owner',
    'role' => 'karenderia_owner',
    'password' => bcrypt('password123'),
    'phone' => '+639123456789',
    'address' => 'Cebu City',
    'is_verified' => true
]);

$karenderia = Karenderia::firstOrCreate([
    'owner_id' => $owner->id
], [
    'name' => 'Test Recipe Kitchen',
    'address' => 'Lahug, Cebu City',
    'latitude' => 10.3157,
    'longitude' => 123.8854,
    'phone' => '+639123456789',
    'description' => 'Test kitchen for recipe management',
    'average_rating' => 4.5,
    'total_reviews' => 10,
    'is_approved' => true,
    'is_active' => true
]);

echo "   Owner created/found: {$owner->name} (ID: {$owner->id})\n";
echo "   Karenderia created/found: {$karenderia->name} (ID: {$karenderia->id})\n\n";

// Test 3: Create a recipe
echo "3. Creating a detailed recipe...\n";

$recipe = Recipe::create([
    'karenderia_id' => $karenderia->id,
    'name' => 'Test Cebuano Humba',
    'description' => 'A traditional Cebuano braised pork dish with sweet and savory sauce',
    'ingredients' => [
        'pork belly' => '1 kg, cut into chunks',
        'soy sauce' => '1/2 cup dark soy sauce',
        'vinegar' => '1/4 cup native vinegar',
        'brown sugar' => '3 tablespoons',
        'star anise' => '3 pieces',
        'bay leaves' => '3 pieces',
        'garlic' => '8 cloves, minced',
        'onion' => '1 large, sliced',
        'black beans' => '2 tablespoons salted black beans',
        'hard boiled eggs' => '4 pieces, peeled'
    ],
    'instructions' => [
        'Heat oil in a heavy-bottomed pot over medium heat',
        'Sauté garlic, onion, and ginger until fragrant',
        'Add pork belly chunks and brown on all sides',
        'Add soy sauce, vinegar, and brown sugar',
        'Add star anise, bay leaves, and black beans',
        'Pour water and bring to boil, then simmer for 1.5 hours',
        'Add hard boiled eggs and cook for 30 more minutes',
        'Serve hot with steamed rice'
    ],
    'prep_time_minutes' => 30,
    'cook_time_minutes' => 120,
    'difficulty_level' => 'medium',
    'servings' => 6,
    'category' => 'Main Course',
    'cuisine_type' => 'Cebuano',
    'cost_estimate' => 450.00,
    'nutritional_info' => [
        'calories_per_serving' => 380,
        'protein_g' => 28,
        'fat_g' => 22,
        'carbs_g' => 15
    ],
    'is_published' => true,
    'is_signature' => true
]);

echo "   Recipe created: {$recipe->name} (ID: {$recipe->id})\n";
echo "   Total time: {$recipe->total_time} minutes\n";
echo "   Cost per serving: ₱{$recipe->cost_per_serving}\n";
echo "   Can create menu item: " . ($recipe->canCreateMenuItem() ? 'Yes' : 'No') . "\n\n";

// Test 4: Create menu item from recipe
echo "4. Creating menu item from recipe...\n";

if ($recipe->canCreateMenuItem()) {
    $menuItem = MenuItem::create([
        'karenderia_id' => $karenderia->id,
        'recipe_id' => $recipe->id,
        'name' => $recipe->name,
        'description' => $recipe->description,
        'price' => 180.00,
        'cost_price' => $recipe->cost_per_serving,
        'category' => $recipe->category,
        'is_available' => true,
        'is_featured' => true,
        'preparation_time_minutes' => $recipe->total_time,
        'calories' => $recipe->nutritional_info['calories_per_serving'] ?? null,
        'ingredients' => array_keys($recipe->ingredients),
        'spice_level' => 1,
        'average_rating' => 0,
        'total_reviews' => 0,
        'total_orders' => 0
    ]);
    
    echo "   Menu item created: {$menuItem->name} - ₱{$menuItem->price}\n";
    echo "   Based on recipe: {$menuItem->recipe->name}\n\n";
} else {
    echo "   Cannot create menu item - recipe missing required details\n\n";
}

// Test 5: Test distance calculations
echo "5. Testing nearby karenderia calculations...\n";

$testLatitude = 10.3157;
$testLongitude = 123.8854;
$testRadius = 10;

$nearbyKarenderias = \DB::select("
    SELECT id, name, address, latitude, longitude,
           (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
           cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
           sin(radians(latitude)))) AS distance
    FROM karenderias 
    WHERE is_active = 1 AND is_approved = 1
    HAVING distance < ?
    ORDER BY distance
", [$testLatitude, $testLongitude, $testLatitude, $testRadius]);

echo "   Search center: {$testLatitude}, {$testLongitude}\n";
echo "   Radius: {$testRadius} km\n";
echo "   Found " . count($nearbyKarenderias) . " nearby karenderias:\n";

foreach ($nearbyKarenderias as $k) {
    echo "   - {$k->name}: " . round($k->distance, 2) . " km away\n";
}

echo "\n=== Recipe Management System Test Complete ===\n";
