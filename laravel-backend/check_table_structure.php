<?php
require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database connection
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Karenderias Table Structure ===\n";
    $stmt = $pdo->prepare("DESCRIBE karenderias");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo $column['Field'] . " | " . $column['Type'] . " | " . $column['Null'] . " | " . $column['Key'] . "\n";
    }
    
    echo "\n=== All Karenderias Data ===\n";
    $stmt = $pdo->prepare("SELECT * FROM karenderias");
    $stmt->execute();
    $karenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($karenderias as $k) {
        echo "ID: " . $k['id'] . "\n";
        echo "Name: " . $k['name'] . "\n";
        echo "Business Name: " . ($k['business_name'] ?? 'NULL') . "\n";
        if (isset($k['owner_id'])) {
            echo "Owner ID: " . $k['owner_id'] . "\n";
        }
        if (isset($k['user_id'])) {
            echo "User ID: " . $k['user_id'] . "\n";
        }
        echo "Created: " . $k['created_at'] . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>