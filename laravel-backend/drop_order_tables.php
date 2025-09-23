<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "🗑️ Dropping order-related tables...\n";

try {
    // Drop order tables
    if (Schema::hasTable('order_items')) {
        Schema::dropIfExists('order_items');
        echo "✅ Dropped order_items table\n";
    } else {
        echo "⚠️ order_items table doesn't exist\n";
    }
    
    if (Schema::hasTable('orders')) {
        Schema::dropIfExists('orders');
        echo "✅ Dropped orders table\n";
    } else {
        echo "⚠️ orders table doesn't exist\n";
    }
    
    echo "🎉 Order tables removed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>