<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Database Connection and Menu Items...\n\n";

try {
    // Test menu items count
    $count = App\Models\MenuItem::count();
    echo "📊 Total Menu Items: $count\n\n";
    
    if ($count > 0) {
        echo "📋 Sample Menu Items:\n";
        $items = App\Models\MenuItem::take(3)->get();
        foreach ($items as $item) {
            echo "- {$item->name} (ID: {$item->id}, Karenderia: {$item->karenderia_id}, Price: ₱{$item->price})\n";
        }
        echo "\n";
        
        // Test items for karenderia ID 1
        $karenderia1Items = App\Models\MenuItem::where('karenderia_id', 1)->get();
        echo "🍽️ Menu Items for Karenderia ID 1: " . $karenderia1Items->count() . "\n";
        
        foreach ($karenderia1Items as $item) {
            echo "- {$item->name} - ₱{$item->price}\n";
        }
    } else {
        echo "⚠️ No menu items found in database!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
