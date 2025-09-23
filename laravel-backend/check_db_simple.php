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
    
    echo "=== User-Karenderia Relationship Check ===\n\n";
    
    // Get all users
    echo "Users:\n";
    $stmt = $pdo->query("SELECT id, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
    
    echo "\nKarenderias:\n";
    $stmt = $pdo->query("SELECT id, name, owner_id FROM karenderias");
    $karenderias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($karenderias as $karenderia) {
        echo "- ID: {$karenderia['id']}, Name: {$karenderia['name']}, Owner ID: {$karenderia['owner_id']}\n";
    }
    
    echo "\nUser-Karenderia Mapping:\n";
    foreach ($users as $user) {
        $found = false;
        foreach ($karenderias as $karenderia) {
            if ($karenderia['owner_id'] == $user['id']) {
                echo "- {$user['email']} owns '{$karenderia['name']}'\n";
                $found = true;
            }
        }
        if (!$found) {
            echo "- {$user['email']} has NO karenderia\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}