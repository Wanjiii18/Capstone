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
    
    echo "=== Testing User ID 2 Karenderia Query ===\n";
    
    // Simulate what the backend API does
    $stmt = $pdo->prepare("SELECT * FROM karenderias WHERE owner_id = 2 LIMIT 1");
    $stmt->execute();
    $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($karenderia) {
        echo "First karenderia for user ID 2:\n";
        echo "- ID: " . $karenderia['id'] . "\n";
        echo "- Name: " . $karenderia['name'] . "\n";
        echo "- Business Name: " . $karenderia['business_name'] . "\n";
        echo "- Description: " . ($karenderia['description'] ?? 'NULL') . "\n";
        echo "- Owner ID: " . $karenderia['owner_id'] . "\n";
        echo "- Status: " . $karenderia['status'] . "\n";
        
        // This is what should be returned by the API
        $apiResponse = [
            'success' => true,
            'data' => [
                'id' => $karenderia['id'],
                'name' => $karenderia['name'],
                'description' => $karenderia['description'],
                'address' => $karenderia['address'],
                'phone' => $karenderia['phone'],
                'email' => $karenderia['email'],
                'status' => $karenderia['status']
            ],
            'message' => 'Karenderia retrieved successfully'
        ];
        
        echo "\nExpected API Response:\n";
        echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "No karenderia found for user ID 2\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>