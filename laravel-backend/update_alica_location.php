<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    
    // Update alica's karenderia with Manila coordinates
    // Using Makati CBD coordinates as an example
    $latitude = 14.5995;   // Manila/Makati latitude
    $longitude = 121.0308; // Manila/Makati longitude
    
    $stmt = $pdo->prepare("UPDATE karenderias SET latitude = ?, longitude = ? WHERE email = 'alica@gmail.com'");
    $result = $stmt->execute([$latitude, $longitude]);
    
    if ($result) {
        echo "✅ Successfully updated Alica Kitchen location!" . PHP_EOL;
        echo "📍 New coordinates: $latitude, $longitude" . PHP_EOL;
        echo "🗺️  Location: Makati CBD, Manila, Philippines" . PHP_EOL;
        
        // Verify the update
        $stmt = $pdo->prepare("SELECT business_name, latitude, longitude, address FROM karenderias WHERE email = 'alica@gmail.com'");
        $stmt->execute();
        $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo PHP_EOL . "📋 Current karenderia info:" . PHP_EOL;
        echo "Business: " . $karenderia['business_name'] . PHP_EOL;
        echo "Address: " . $karenderia['address'] . PHP_EOL;
        echo "Coordinates: " . $karenderia['latitude'] . ", " . $karenderia['longitude'] . PHP_EOL;
        
    } else {
        echo "❌ Failed to update location" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}