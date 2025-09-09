<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "🧪 Quick Allergen Setup for Testing\n\n";

try {
    // Get user by email (you can change this to your test email)
    $email = $argv[1] ?? 'test@kaplato.com';
    $user = App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        echo "❌ User with email '$email' not found!\n";
        echo "💡 Usage: php setup_test_allergens.php your@email.com\n";
        exit(1);
    }

    echo "✅ Found user: {$user->name} (ID: {$user->id})\n";

    // Clear existing allergens
    $user->allergens()->delete();
    echo "🧹 Cleared existing allergens\n";

    // Add common allergens for testing
    $allergens = [
        ['name' => 'nuts', 'category' => 'tree nuts', 'severity' => 'severe'],
        ['name' => 'dairy', 'category' => 'dairy products', 'severity' => 'moderate']
    ];

    foreach ($allergens as $allergenData) {
        $allergen = $user->allergens()->create($allergenData);
        echo "➕ Added: {$allergen->name} ({$allergen->severity})\n";
    }

    echo "\n🎯 Test Setup Complete!\n";
    echo "📋 User ID: {$user->id}\n";
    echo "📧 Email: {$user->email}\n";
    echo "🚨 Allergens: " . $user->allergens->pluck('name')->implode(', ') . "\n";
    echo "\n💡 Now when you click karenderia markers on the map, you'll see allergy warnings!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
