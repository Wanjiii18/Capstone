<?php

// Check for duplicate menu items in PostgreSQL database
echo "Checking for duplicate menu items...\n";

try {
    // Connect to PostgreSQL
    $pdo = new PDO('pgsql:host=127.0.0.1;dbname=kaplato_db', 'postgres', 'password');
    
    echo "\n=== CHECKING MENU ITEMS FOR DUPLICATES ===\n";
    
    // Get all menu items with karenderia info
    $stmt = $pdo->query('
        SELECT 
            mi.id, 
            mi.name, 
            mi.karenderia_id,
            k.business_name,
            mi.created_at,
            mi.updated_at
        FROM menu_items mi 
        LEFT JOIN karenderias k ON mi.karenderia_id = k.id 
        ORDER BY mi.name, mi.karenderia_id
    ');
    
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total menu items found: " . count($menuItems) . "\n\n";
    
    // Group by name to find duplicates
    $groupedItems = [];
    foreach ($menuItems as $item) {
        $name = $item['name'];
        if (!isset($groupedItems[$name])) {
            $groupedItems[$name] = [];
        }
        $groupedItems[$name][] = $item;
    }
    
    echo "=== DUPLICATE ANALYSIS ===\n";
    $duplicatesFound = false;
    
    foreach ($groupedItems as $name => $items) {
        if (count($items) > 1) {
            $duplicatesFound = true;
            echo "\n🔍 DUPLICATE FOUND: '$name' (" . count($items) . " entries)\n";
            
            foreach ($items as $item) {
                echo "  - ID: {$item['id']} | Karenderia: {$item['karenderia_id']} ({$item['business_name']}) | Created: {$item['created_at']}\n";
            }
        }
    }
    
    if (!$duplicatesFound) {
        echo "✅ No duplicates found!\n";
    }
    
    echo "\n=== ALL MENU ITEMS BY KARENDERIA ===\n";
    $karenderiaItems = [];
    
    foreach ($menuItems as $item) {
        $karenderiaId = $item['karenderia_id'];
        if (!isset($karenderiaItems[$karenderiaId])) {
            $karenderiaItems[$karenderiaId] = [
                'business_name' => $item['business_name'],
                'items' => []
            ];
        }
        $karenderiaItems[$karenderiaId]['items'][] = $item;
    }
    
    foreach ($karenderiaItems as $karenderiaId => $data) {
        echo "\n📍 Karenderia ID {$karenderiaId}: {$data['business_name']}\n";
        echo "   Items (" . count($data['items']) . "):\n";
        
        foreach ($data['items'] as $item) {
            echo "   - {$item['name']} (ID: {$item['id']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure PostgreSQL is running and database credentials are correct.\n";
}
