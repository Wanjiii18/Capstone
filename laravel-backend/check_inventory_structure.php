<?php

// Check actual database structure
$host = '127.0.0.1';
$port = '3306';
$dbname = 'kaplato_db';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Current Database Structure Analysis ===\n\n";
    
    // Check daily_menus table structure
    echo "DAILY_MENUS Table Structure:\n";
    $stmt = $pdo->query("DESCRIBE daily_menus");
    $dailyMenuFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($dailyMenuFields as $field) {
        echo "- {$field['Field']} ({$field['Type']}) {$field['Null']} {$field['Key']}\n";
    }
    
    // Check if inventory table exists
    echo "\nINVENTORY Table Structure:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'inventory'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("DESCRIBE inventory");
        $inventoryFields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($inventoryFields as $field) {
            echo "- {$field['Field']} ({$field['Type']}) {$field['Null']} {$field['Key']}\n";
        }
        
        // Check inventory data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM inventory");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Inventory records: {$count['count']}\n";
        
    } else {
        echo "❌ Inventory table does not exist!\n";
    }
    
    // Check if daily_menus has inventory_id field
    echo "\nAnalysis:\n";
    $hasInventoryId = false;
    foreach ($dailyMenuFields as $field) {
        if ($field['Field'] === 'inventory_id') {
            $hasInventoryId = true;
            break;
        }
    }
    
    if ($hasInventoryId) {
        echo "✅ daily_menus table has inventory_id field\n";
    } else {
        echo "❌ daily_menus table missing inventory_id field\n";
        echo "🔧 Need to add inventory integration\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}