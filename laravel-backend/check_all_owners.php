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
    
    echo "=== All Karenderia Owners ===\n\n";
    
    // Get all users with karenderia_owner role
    $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE role = 'karenderia_owner' ORDER BY id");
    $stmt->execute();
    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($owners as $owner) {
        echo "USER ID: " . $owner['id'] . "\n";
        echo "Name: " . $owner['name'] . "\n";
        echo "Email: " . $owner['email'] . "\n";
        echo "Role: " . $owner['role'] . "\n";
        
        // Get their karenderias
        $stmt2 = $pdo->prepare("SELECT id, name, business_name, status FROM karenderias WHERE owner_id = ?");
        $stmt2->execute([$owner['id']]);
        $karenderias = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        if ($karenderias) {
            echo "Karenderias:\n";
            foreach ($karenderias as $k) {
                echo "  - ID: " . $k['id'] . " | Name: " . $k['name'] . " | Business: " . $k['business_name'] . " | Status: " . $k['status'] . "\n";
            }
        } else {
            echo "No karenderias found\n";
        }
        echo "---\n";
    }
    
    echo "\n=== Test Login Credentials ===\n";
    echo "Based on the typical pattern, try these credentials:\n\n";
    
    foreach ($owners as $owner) {
        $emailParts = explode('@', $owner['email']);
        $username = $emailParts[0];
        echo "Email: " . $owner['email'] . "\n";
        echo "Likely password: " . $username . "123 (e.g., owner123, admin123, etc.)\n";
        echo "Alternative: password123 or karenderia123\n\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>