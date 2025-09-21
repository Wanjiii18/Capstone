<?php
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    
    echo "=== ALL KARENDERIAS IN DATABASE ===\n";
    $stmt = $pdo->query("SELECT id, name, status, latitude, longitude, address, created_at FROM karenderias ORDER BY created_at DESC");
    $karenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($karenderias)) {
        echo "No karenderias found in database.\n";
    } else {
        foreach ($karenderias as $k) {
            echo "ID: {$k['id']}\n";
            echo "Name: {$k['name']}\n";
            echo "Status: {$k['status']}\n";
            echo "Address: {$k['address']}\n";
            echo "Coordinates: {$k['latitude']}, {$k['longitude']}\n";
            echo "Created: {$k['created_at']}\n";
            echo "---\n";
        }
    }
    
    echo "\n=== STATUS COUNTS ===\n";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM karenderias GROUP BY status");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($counts as $count) {
        echo "{$count['status']}: {$count['count']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>