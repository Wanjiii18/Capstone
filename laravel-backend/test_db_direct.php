<?php

echo "Checking database connection...\n";

// Simple database check without Laravel
$host = 'localhost';
$dbname = 'kaplato_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database!\n\n";
    
    // Count total karenderias
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM karenderias");
    $count = $stmt->fetch()['count'];
    echo "Total karenderias in database: $count\n\n";
    
    // Get Talisay karenderias
    $stmt = $pdo->query("SELECT id, name, latitude, longitude, address, status FROM karenderias WHERE address LIKE '%Talisay%' OR name LIKE '%Talisay%'");
    $talisayKarenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Talisay karenderias found: " . count($talisayKarenderias) . "\n";
    
    foreach ($talisayKarenderias as $k) {
        echo "- {$k['id']}: {$k['name']} at {$k['address']} ({$k['latitude']}, {$k['longitude']}) - Status: {$k['status']}\n";
    }
    
    // Test distance calculation
    echo "\nTesting distance calculation (within 15km of Talisay City):\n";
    $lat = 10.2442;
    $lng = 123.8492;
    
    $stmt = $pdo->prepare("
        SELECT 
            id,
            name,
            latitude,
            longitude,
            address,
            status,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
        FROM karenderias 
        WHERE status = 'active'
        HAVING distance < 15
        ORDER BY distance
        LIMIT 20
    ");
    
    $stmt->execute([$lat, $lng, $lat]);
    $nearby = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($nearby) . " nearby karenderias:\n";
    foreach ($nearby as $k) {
        echo "- {$k['name']} ({$k['distance']}km) at {$k['address']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
