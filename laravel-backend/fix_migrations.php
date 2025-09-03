<?php

// Simple script to mark migrations as run
echo "Fixing migration duplications...\n";

try {
    // Use database credentials from .env
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    
    $migrations = [
        '2025_07_13_000000_create_karenderias_table',
        '2025_07_14_064533_create_karenderias_table',
        '2025_08_12_000002_add_recipe_id_to_menu_items_table'
    ];
    
    foreach ($migrations as $migration) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO migrations (migration, batch) VALUES (?, 2)');
        $result = $stmt->execute([$migration]);
        echo "✅ Marked $migration as run\n";
    }
    
    echo "\n🎉 Migration fix complete! Now run: php artisan migrate\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}
