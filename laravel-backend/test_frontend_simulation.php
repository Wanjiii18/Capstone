<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Menu Creation with Missing Nutritional Data ===\n\n";

// Clear the log to see fresh output
$logPath = storage_path('logs/laravel.log');
file_put_contents($logPath, '');

// Simulate what the frontend is sending (without calories)
$frontendData = [
    'name' => 'Frontend Test Menu',
    'description' => 'Menu created without calories field',
    'price' => 150.00,
    'category' => 'Main Course',
    'ingredients' => [
        ['ingredientName' => 'Rice', 'quantity' => 1, 'unit' => 'cup', 'cost' => 20],
        ['ingredientName' => 'Chicken', 'quantity' => 200, 'unit' => 'g', 'cost' => 80]
    ],
    'allergens' => [], // Empty array like frontend sends
    'is_available' => true,
    'is_popular' => false,
    'preparation_time' => 20
    // Notice: NO calories field
];

echo "1. Simulating frontend request data:\n";
echo "   " . json_encode($frontendData, JSON_PRETTY_PRINT) . "\n\n";

try {
    // Create a fake request object to test the controller logic
    $request = new \Illuminate\Http\Request();
    $request->replace($frontendData);
    
    // Simulate authentication
    $user = App\Models\User::where('role', 'karenderia_owner')->first();
    Auth::login($user);
    
    echo "2. Testing controller validation and processing...\n";
    
    // Test the validation rules
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'nullable|string',
        'category' => 'nullable|string|max:255',
        'karenderia_id' => 'nullable|exists:karenderias,id',
        'ingredients' => 'nullable|array',
        'ingredients.*.ingredientName' => 'string|max:255',
        'ingredients.*.quantity' => 'nullable|numeric',
        'ingredients.*.unit' => 'nullable|string|max:50',
        'ingredients.*.cost' => 'nullable|numeric',
        'allergens' => 'nullable|array',
        'allergens.*' => 'string|max:255',
        'calories' => 'nullable|integer|min:0',
        'spice_level' => 'nullable|integer|min:1|max:5',
        'image_url' => 'nullable|string|url',
        'is_available' => 'nullable|boolean',
        'is_popular' => 'nullable|boolean',
        'preparation_time' => 'nullable|integer|min:0',
        'preparationTime' => 'nullable|integer|min:0',
        'created_at' => 'nullable|string',
        'updated_at' => 'nullable|string'
    ]);
    
    echo "✅ Validation passed\n";
    echo "   Validated allergens: " . json_encode($validatedData['allergens']) . "\n";
    echo "   Validated calories: " . ($validatedData['calories'] ?? 'NOT_PROVIDED') . "\n\n";
    
    // Apply the controller's processing logic
    if (!isset($validatedData['allergens'])) {
        $validatedData['allergens'] = [];
    } elseif (!is_array($validatedData['allergens'])) {
        $validatedData['allergens'] = [];
    }

    if (!isset($validatedData['calories']) || $validatedData['calories'] === null) {
        $validatedData['calories'] = 0;
    }

    if (!isset($validatedData['category'])) {
        $validatedData['category'] = 'Main Dish';
    }

    // Process ingredients
    if (isset($validatedData['ingredients']) && is_array($validatedData['ingredients'])) {
        $simpleIngredients = [];
        foreach ($validatedData['ingredients'] as $ingredient) {
            if (is_array($ingredient) && isset($ingredient['ingredientName'])) {
                $simpleIngredients[] = $ingredient['ingredientName'];
            } elseif (is_string($ingredient)) {
                $simpleIngredients[] = $ingredient;
            }
        }
        $validatedData['ingredients'] = $simpleIngredients;
    }

    // Add karenderia_id
    $karenderia = App\Models\Karenderia::where('owner_id', $user->id)->first();
    $validatedData['karenderia_id'] = $karenderia->id;

    // Remove timestamps
    unset($validatedData['created_at'], $validatedData['updated_at']);

    echo "3. Final processed data for database:\n";
    echo "   " . json_encode($validatedData, JSON_PRETTY_PRINT) . "\n\n";

    // Create the menu item
    $menuItem = App\Models\MenuItem::create($validatedData);
    
    echo "4. ✅ Menu item created successfully!\n";
    echo "   ID: {$menuItem->id}\n";
    echo "   Name: {$menuItem->name}\n";
    echo "   Calories: {$menuItem->calories}\n";
    echo "   Allergens: " . json_encode($menuItem->allergens) . "\n";
    echo "   Allergens count: " . count($menuItem->allergens ?? []) . "\n\n";

    // Test retrieval
    echo "5. Testing data retrieval:\n";
    $retrieved = App\Models\MenuItem::find($menuItem->id);
    echo "   Retrieved calories: {$retrieved->calories}\n";
    echo "   Retrieved allergens: " . json_encode($retrieved->allergens) . "\n";
    echo "   Retrieved allergens are array: " . (is_array($retrieved->allergens) ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Summary ===\n";
echo "The issue was:\n";
echo "1. Frontend not sending 'calories' field\n";
echo "2. Allergens being sent as empty array []\n";
echo "3. Controller now defaults calories to 0 when not provided\n";
echo "4. Controller ensures allergens is always an array\n";
echo "\nFor proper functionality, the frontend should:\n";
echo "1. Always send 'calories' field (even if 0)\n";
echo "2. Send allergens as array of strings\n";
echo "3. Example: {\"calories\": 250, \"allergens\": [\"nuts\", \"dairy\"]}\n";
