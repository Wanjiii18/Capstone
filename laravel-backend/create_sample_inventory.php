<?php

// Create sample inventory data for both karenderias
$host = '127.0.0.1';
$port = '3306';
$dbname = 'kaplato_db';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Creating Sample Inventory Data ===\n\n";
    
    // Get karenderias
    $stmt = $pdo->query("SELECT id, name FROM karenderias");
    $karenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($karenderias as $karenderia) {
        echo "Creating inventory for {$karenderia['name']} (ID: {$karenderia['id']}):\n";
        
        // Common ingredients for Filipino dishes
        $inventoryItems = [
            // Proteins
            [
                'item_name' => 'Chicken (Whole)',
                'description' => 'Fresh whole chicken for adobo, tinola, etc.',
                'category' => 'Protein',
                'unit' => 'kg',
                'current_stock' => 25.000,
                'minimum_stock' => 5.000,
                'maximum_stock' => 50.000,
                'unit_cost' => 180.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Beef (Brisket)',
                'description' => 'Beef brisket for sinigang, kare-kare',
                'category' => 'Protein',
                'unit' => 'kg',
                'current_stock' => 15.000,
                'minimum_stock' => 3.000,
                'maximum_stock' => 30.000,
                'unit_cost' => 450.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Pork Belly',
                'description' => 'Pork belly for sisig, adobo',
                'category' => 'Protein',
                'unit' => 'kg',
                'current_stock' => 20.000,
                'minimum_stock' => 5.000,
                'maximum_stock' => 40.000,
                'unit_cost' => 320.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Beef Tapa',
                'description' => 'Marinated beef strips for tapsilog',
                'category' => 'Protein',
                'unit' => 'kg',
                'current_stock' => 10.000,
                'minimum_stock' => 2.000,
                'maximum_stock' => 20.000,
                'unit_cost' => 380.00,
                'status' => 'available'
            ],
            
            // Grains & Starches
            [
                'item_name' => 'Jasmine Rice',
                'description' => 'Premium jasmine rice',
                'category' => 'Grains',
                'unit' => 'kg',
                'current_stock' => 100.000,
                'minimum_stock' => 25.000,
                'maximum_stock' => 200.000,
                'unit_cost' => 45.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Eggs',
                'description' => 'Fresh chicken eggs',
                'category' => 'Protein',
                'unit' => 'dozen',
                'current_stock' => 30.000,
                'minimum_stock' => 10.000,
                'maximum_stock' => 60.000,
                'unit_cost' => 120.00,
                'status' => 'available'
            ],
            
            // Vegetables
            [
                'item_name' => 'Kangkong',
                'description' => 'Water spinach for sinigang',
                'category' => 'Vegetables',
                'unit' => 'kg',
                'current_stock' => 8.000,
                'minimum_stock' => 2.000,
                'maximum_stock' => 15.000,
                'unit_cost' => 30.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Radish',
                'description' => 'White radish for sinigang',
                'category' => 'Vegetables',
                'unit' => 'kg',
                'current_stock' => 12.000,
                'minimum_stock' => 3.000,
                'maximum_stock' => 20.000,
                'unit_cost' => 25.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Onions',
                'description' => 'Yellow onions',
                'category' => 'Vegetables',
                'unit' => 'kg',
                'current_stock' => 15.000,
                'minimum_stock' => 5.000,
                'maximum_stock' => 30.000,
                'unit_cost' => 60.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Garlic',
                'description' => 'Fresh garlic',
                'category' => 'Seasonings',
                'unit' => 'kg',
                'current_stock' => 5.000,
                'minimum_stock' => 1.000,
                'maximum_stock' => 10.000,
                'unit_cost' => 200.00,
                'status' => 'available'
            ],
            
            // Pantry Items
            [
                'item_name' => 'Soy Sauce',
                'description' => 'Premium soy sauce',
                'category' => 'Condiments',
                'unit' => 'liter',
                'current_stock' => 5.000,
                'minimum_stock' => 2.000,
                'maximum_stock' => 10.000,
                'unit_cost' => 35.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Vinegar',
                'description' => 'White vinegar',
                'category' => 'Condiments',
                'unit' => 'liter',
                'current_stock' => 8.000,
                'minimum_stock' => 3.000,
                'maximum_stock' => 15.000,
                'unit_cost' => 25.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Cooking Oil',
                'description' => 'Vegetable cooking oil',
                'category' => 'Pantry',
                'unit' => 'liter',
                'current_stock' => 20.000,
                'minimum_stock' => 5.000,
                'maximum_stock' => 40.000,
                'unit_cost' => 75.00,
                'status' => 'available'
            ],
            
            // Dessert Ingredients
            [
                'item_name' => 'Mixed Beans',
                'description' => 'Mixed beans for halo-halo',
                'category' => 'Dessert',
                'unit' => 'kg',
                'current_stock' => 3.000,
                'minimum_stock' => 1.000,
                'maximum_stock' => 5.000,
                'unit_cost' => 80.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Condensed Milk',
                'description' => 'Sweetened condensed milk',
                'category' => 'Dessert',
                'unit' => 'can',
                'current_stock' => 24.000,
                'minimum_stock' => 6.000,
                'maximum_stock' => 48.000,
                'unit_cost' => 45.00,
                'status' => 'available'
            ],
            [
                'item_name' => 'Ice Cream',
                'description' => 'Vanilla ice cream for halo-halo',
                'category' => 'Dessert',
                'unit' => 'liter',
                'current_stock' => 5.000,
                'minimum_stock' => 2.000,
                'maximum_stock' => 10.000,
                'unit_cost' => 150.00,
                'status' => 'available'
            ]
        ];
        
        foreach ($inventoryItems as $item) {
            $item['karenderia_id'] = $karenderia['id'];
            $item['total_value'] = $item['current_stock'] * $item['unit_cost'];
            $item['supplier'] = 'Local Market';
            $item['last_restocked'] = date('Y-m-d', strtotime('-3 days'));
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
            
            $columns = implode(', ', array_keys($item));
            $placeholders = ':' . implode(', :', array_keys($item));
            $sql = "INSERT INTO inventory ($columns) VALUES ($placeholders)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($item);
            
            echo "  ✅ {$item['item_name']} - {$item['current_stock']} {$item['unit']} @ ₱{$item['unit_cost']}\n";
        }
        
        echo "\n";
    }
    
    // Get total inventory count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM inventory");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "🎉 Created {$count['count']} inventory items across " . count($karenderias) . " karenderias!\n";
    echo "📊 Each karenderia now has comprehensive ingredient inventory\n";
    echo "🍽️ Ready to link menu items with ingredient requirements\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}