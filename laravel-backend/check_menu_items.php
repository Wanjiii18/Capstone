<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\MenuItem;

try {
    $menuItems = MenuItem::all();
    echo "Total Menu Items: " . $menuItems->count() . "\n\n";
    
    if ($menuItems->count() > 0) {
        echo "Menu Items:\n";
        echo "===========\n";
        foreach ($menuItems as $item) {
            echo "- " . $item->name . " (₱" . number_format($item->price, 2) . ")\n";
            echo "  Description: " . ($item->description ?? 'No description') . "\n";
            echo "  Category: " . ($item->category ?? 'No category') . "\n";
            echo "  Created: " . $item->created_at . "\n\n";
        }
    } else {
        echo "No menu items found in database.\n";
        echo "These might be:\n";
        echo "1. Mock/demo data displayed in the frontend\n";
        echo "2. Data from a seeder that hasn't been run\n";
        echo "3. Data from a different database/environment\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}