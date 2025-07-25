<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;

try {
    echo "Testing database connection...\n";
    
    // Count existing orders
    $orderCount = Order::count();
    echo "Total orders in database: " . $orderCount . "\n";
    
    // Get latest order
    $latestOrder = Order::latest()->first();
    if ($latestOrder) {
        echo "Latest order ID: " . $latestOrder->id . "\n";
        echo "Latest order status: " . $latestOrder->status . "\n";
        echo "Latest order amount: " . $latestOrder->total_amount . "\n";
        echo "Latest order created: " . $latestOrder->created_at . "\n";
    } else {
        echo "No orders found in database\n";
    }
    
    echo "\nDatabase connection successful!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
