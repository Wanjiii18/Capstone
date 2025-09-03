<?php

// Check database data and add missing column
echo "Checking database data and adding missing column...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    
    // Check if detailed_ingredients column exists
    $stmt = $pdo->query('DESCRIBE menu_items');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasDetailedIngredients = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'detailed_ingredients') {
            $hasDetailedIngredients = true;
            break;
        }
    }
    
    if (!$hasDetailedIngredients) {
        echo "❌ detailed_ingredients column missing! Adding it...\n";
        $pdo->exec('ALTER TABLE menu_items ADD COLUMN detailed_ingredients JSON NULL AFTER ingredients');
        echo "✅ Added detailed_ingredients column\n";
    } else {
        echo "✅ detailed_ingredients column already exists\n";
    }
    
    // Add migration record
    $stmt = $pdo->prepare('INSERT IGNORE INTO migrations (migration, batch) VALUES (?, 3)');
    $stmt->execute(['2025_09_03_000001_add_detailed_ingredients_to_menu_items_table']);
    echo "✅ Migration record added\n";
    
    echo "\n🎉 Database is ready for complex ingredients!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
