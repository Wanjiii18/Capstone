<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Create the database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'kaplato_db',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "=== Testing Database Connection ===\n";
    
    // Test connection
    $pdo = $capsule->getConnection()->getPdo();
    echo "✅ Database connected successfully!\n\n";
    
    // Check if karenderias table exists
    $tables = $capsule->select("SHOW TABLES LIKE 'karenderias'");
    if (empty($tables)) {
        echo "❌ Table 'karenderias' does not exist!\n";
        exit;
    }
    
    echo "✅ Table 'karenderias' exists\n\n";
    
    // Count karenderias
    $count = $capsule->table('karenderias')->count();
    echo "📊 Total karenderias: $count\n\n";
    
    if ($count > 0) {
        echo "=== Karenderias List ===\n";
        $karenderias = $capsule->table('karenderias')->get();
        
        foreach ($karenderias as $k) {
            echo "🏪 {$k->name}\n";
            echo "   📍 {$k->address}\n";
            echo "   🗺️  Lat: {$k->latitude}, Lng: {$k->longitude}\n";
            echo "   🟢 Status: {$k->status}\n\n";
        }
        
        // Test nearby calculation
        echo "=== Testing Nearby Calculation ===\n";
        $lat = 10.3234;
        $lng = 123.9312;
        echo "Search coordinates: $lat, $lng\n";
        
        foreach ($karenderias as $k) {
            $distance = haversineDistance($lat, $lng, $k->latitude, $k->longitude);
            echo "Distance to {$k->name}: " . round($distance, 2) . " meters\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

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
