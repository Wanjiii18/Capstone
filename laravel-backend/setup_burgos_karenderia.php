<?php

require_once 'vendor/autoload.php';

// Simple database connection
$host = '127.0.0.1';
$port = '3306';
$dbname = 'kaplato_db';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Creating Karenderia for burgos123@gmail.com ===\n\n";
    
    // Check if burgos123@gmail.com exists
    $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE email = ?");
    $stmt->execute(['burgos123@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ User burgos123@gmail.com not found!\n";
        exit;
    }
    
    echo "✅ Found user: {$user['email']} (ID: {$user['id']}, Role: {$user['role']})\n";
    
    // Check if they already have a karenderia
    $stmt = $pdo->prepare("SELECT * FROM karenderias WHERE owner_id = ?");
    $stmt->execute([$user['id']]);
    $existingKarenderia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingKarenderia) {
        echo "ℹ️  User already has a karenderia: {$existingKarenderia['name']}\n";
        exit;
    }
    
    // Create karenderia for the user
    $karenderiaData = [
        'name' => 'Burgos Family Kitchen',
        'business_name' => 'Burgos Family Kitchen',
        'description' => 'Authentic Filipino home-cooked meals',
        'address' => '123 Main Street, Quezon City',
        'city' => 'Quezon City',
        'province' => 'Metro Manila',
        'phone' => '09123456789',
        'email' => 'burgos123@gmail.com',
        'business_email' => 'business@burgosfamilykitchen.com',
        'owner_id' => $user['id'],
        'latitude' => 14.6760,
        'longitude' => 121.0437,
        'opening_time' => '08:00:00',
        'closing_time' => '20:00:00',
        'operating_days' => '["monday","tuesday","wednesday","thursday","friday","saturday"]',
        'status' => 'approved',
        'approved_at' => date('Y-m-d H:i:s'),
        'delivery_fee' => 50.00,
        'delivery_time_minutes' => 30,
        'accepts_cash' => 1,
        'accepts_online_payment' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $columns = implode(', ', array_keys($karenderiaData));
    $placeholders = ':' . implode(', :', array_keys($karenderiaData));
    
    $sql = "INSERT INTO karenderias ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($karenderiaData);
    
    $karenderiaId = $pdo->lastInsertId();
    echo "✅ Created karenderia 'Burgos Family Kitchen' (ID: $karenderiaId) for {$user['email']}\n";
    
    // Now create some sample menu items for this karenderia
    $menuItems = [
        [
            'name' => 'Adobo Chicken',
            'description' => 'Traditional Filipino chicken adobo with rice',
            'price' => 120.00,
            'category' => 'Main Course',
            'is_available' => 1,
            'karenderia_id' => $karenderiaId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Beef Sinigang',
            'description' => 'Sour beef soup with vegetables',
            'price' => 150.00,
            'category' => 'Main Course',
            'is_available' => 1,
            'karenderia_id' => $karenderiaId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Pork Sisig',
            'description' => 'Sizzling pork sisig with rice',
            'price' => 130.00,
            'category' => 'Main Course',
            'is_available' => 1,
            'karenderia_id' => $karenderiaId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Tapsilog',
            'description' => 'Beef tapa with garlic rice and fried egg',
            'price' => 100.00,
            'category' => 'Breakfast',
            'is_available' => 1,
            'karenderia_id' => $karenderiaId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Halo-Halo',
            'description' => 'Traditional Filipino mixed dessert',
            'price' => 80.00,
            'category' => 'Dessert',
            'is_available' => 1,
            'karenderia_id' => $karenderiaId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    foreach ($menuItems as $item) {
        $columns = implode(', ', array_keys($item));
        $placeholders = ':' . implode(', :', array_keys($item));
        $sql = "INSERT INTO menu_items ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($item);
        echo "  ✅ Added menu item: {$item['name']}\n";
    }
    
    echo "\n🎉 Setup complete! burgos123@gmail.com can now access Daily Menu Management\n";
    echo "📱 Log in with: burgos123@gmail.com to test the daily menu system\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}