<?php

// Simple test to check database and API
require_once 'vendor/autoload.php';

// Simulate Laravel environment
$_ENV['APP_ENV'] = 'local';
$_ENV['DB_CONNECTION'] = 'mysql';
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_DATABASE'] = 'kaplato_db';
$_ENV['DB_USERNAME'] = 'root';
$_ENV['DB_PASSWORD'] = '';

echo "=== Database Test ===\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    echo "✅ Database connected\n";
    
    // Check karenderias table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM karenderias");
    $count = $stmt->fetch()['count'];
    echo "📊 Karenderias count: $count\n";
    
    if ($count > 0) {
        echo "\n=== Karenderias List ===\n";
        $stmt = $pdo->query("SELECT id, name, address, latitude, longitude, status FROM karenderias LIMIT 5");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "🏪 {$row['name']}\n";
            echo "   📍 {$row['address']}\n";
            echo "   🗺️  Lat: {$row['latitude']}, Lng: {$row['longitude']}\n";
            echo "   🟢 Status: {$row['status']}\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Distance Calculation ===\n";

function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // Earth's radius in meters
    
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);
    
    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;
    
    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         cos($lat1Rad) * cos($lat2Rad) *
         sin($deltaLon / 2) * sin($deltaLon / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earthRadius * $c;
}

// Test coordinates - Mandaue City, Cebu
$searchLat = 10.3234;
$searchLng = 123.9312;

echo "Search coordinates: $searchLat, $searchLng\n";

if (isset($pdo) && $count > 0) {
    $stmt = $pdo->query("SELECT name, latitude, longitude FROM karenderias");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $distance = haversineDistance($searchLat, $searchLng, $row['latitude'], $row['longitude']);
        echo "Distance to {$row['name']}: " . round($distance, 2) . " meters\n";
    }
}
