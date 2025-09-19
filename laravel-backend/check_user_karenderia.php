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
    
    echo "=== Checking User ID 2 and Associated Karenderia ===\n\n";
    
    // Get user info
    $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = 2");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found:\n";
        echo "- ID: " . $user['id'] . "\n";
        echo "- Name: " . $user['name'] . "\n";
        echo "- Email: " . $user['email'] . "\n";
        echo "- Role: " . $user['role'] . "\n\n";
        
        // Get karenderia info for this user
        $stmt = $pdo->prepare("SELECT * FROM karenderias WHERE user_id = 2");
        $stmt->execute();
        $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($karenderia) {
            echo "Associated Karenderia:\n";
            echo "- Karenderia ID: " . $karenderia['id'] . "\n";
            echo "- Name: " . $karenderia['name'] . "\n";
            echo "- Business Name: " . ($karenderia['business_name'] ?? 'NULL') . "\n";
            echo "- Description: " . ($karenderia['description'] ?? 'NULL') . "\n";
            echo "- User ID: " . $karenderia['user_id'] . "\n";
            echo "- Created: " . $karenderia['created_at'] . "\n";
            echo "- Updated: " . $karenderia['updated_at'] . "\n\n";
        } else {
            echo "No karenderia found for user ID 2\n\n";
        }
        
        // Check all karenderias to see what exists
        echo "=== All Karenderias in Database ===\n";
        $stmt = $pdo->prepare("SELECT id, name, business_name, user_id FROM karenderias ORDER BY id");
        $stmt->execute();
        $karenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($karenderias as $k) {
            echo "ID: " . $k['id'] . " | Name: " . $k['name'] . " | Business: " . ($k['business_name'] ?? 'NULL') . " | User: " . $k['user_id'] . "\n";
        }
        
    } else {
        echo "User ID 2 not found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>