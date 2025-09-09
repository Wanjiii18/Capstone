<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "🧪 Testing Allergy Warning System...\n\n";

try {
    // Find or create a test user
    $user = App\Models\User::where('email', 'test@kaplato.com')->first();
    if (!$user) {
        echo "⚠️ Test user not found. Creating one...\n";
        $user = App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@kaplato.com',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);
        echo "✅ Test user created with ID: {$user->id}\n";
    } else {
        echo "✅ Found test user with ID: {$user->id}\n";
    }

    // Clear existing allergens
    $user->allergens()->delete();
    echo "🧹 Cleared existing allergens\n";

    // Add some test allergens
    $testAllergens = [
        ['name' => 'nuts', 'category' => 'tree nuts', 'severity' => 'severe'],
        ['name' => 'dairy', 'category' => 'dairy products', 'severity' => 'moderate'],
        ['name' => 'gluten', 'category' => 'grains', 'severity' => 'mild']
    ];

    foreach ($testAllergens as $allergenData) {
        $allergen = $user->allergens()->create($allergenData);
        echo "➕ Added allergen: {$allergen->name} ({$allergen->severity})\n";
    }

    echo "\n📊 User {$user->name} now has the following allergens:\n";
    foreach ($user->allergens as $allergen) {
        echo "- {$allergen->name} ({$allergen->category}) - {$allergen->severity}\n";
    }

    echo "\n🔍 Testing menu items API with user allergens...\n";
    
    // Create a fake request
    $request = new Illuminate\Http\Request();
    $request->query->set('karenderia', '1');
    $request->query->set('user_id', $user->id);
    
    // Call the controller
    $controller = new App\Http\Controllers\MenuItemController();
    $response = $controller->search($request);
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "\n📋 Menu Items with Allergy Warnings:\n";
    foreach ($data['data'] as $item) {
        $warningIndicator = $item['hasDangerousAllergens'] ? '⚠️ DANGER' : '✅ Safe';
        echo "- {$item['name']} - {$warningIndicator}\n";
        
        if ($item['hasDangerousAllergens']) {
            echo "  ⚠️ {$item['allergyMessage']}\n";
        }
        
        if (!empty($item['allergens'])) {
            echo "  Allergens: " . implode(', ', $item['allergens']) . "\n";
        }
        echo "\n";
    }

    echo "🏁 Test completed successfully!\n";
    echo "User ID for testing: {$user->id}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
