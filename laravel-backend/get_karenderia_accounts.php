<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');
    
    echo "=== KARENDERIA ACCOUNTS ===\n\n";
    
    // Get all users with karenderia_owner role
    $stmt = $pdo->query("
        SELECT u.id, u.name, u.email, u.role, u.created_at,
               k.business_name, k.status as karenderia_status
        FROM users u 
        LEFT JOIN karenderias k ON u.id = k.owner_id 
        WHERE u.role = 'karenderia_owner'
        ORDER BY u.created_at
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No karenderia owners found!\n";
        exit;
    }
    
    foreach ($users as $user) {
        echo "----------------------------------------\n";
        echo "USER ID: " . $user['id'] . "\n";
        echo "NAME: " . $user['name'] . "\n";
        echo "EMAIL: " . $user['email'] . "\n";
        echo "BUSINESS NAME: " . ($user['business_name'] ?? 'Not set') . "\n";
        echo "STATUS: " . ($user['karenderia_status'] ?? 'No karenderia record') . "\n";
        echo "REGISTERED: " . $user['created_at'] . "\n";
        
        // Try common passwords
        $commonPasswords = ['password123', 'password', '12345678', 'test123', 'admin123'];
        $foundPassword = false;
        
        foreach ($commonPasswords as $testPassword) {
            // Get the actual password hash
            $hashStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $hashStmt->execute([$user['id']]);
            $hash = $hashStmt->fetchColumn();
            
            if (password_verify($testPassword, $hash)) {
                echo "PASSWORD: " . $testPassword . " ✅\n";
                $foundPassword = true;
                break;
            }
        }
        
        if (!$foundPassword) {
            echo "PASSWORD: Unknown (not a common password)\n";
            echo "HASH: " . substr($hash, 0, 30) . "...\n";
        }
        
        echo "\n";
    }
    
    echo "=== QUICK LOGIN CREDENTIALS ===\n";
    foreach ($users as $user) {
        $hashStmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $hashStmt->execute([$user['id']]);
        $hash = $hashStmt->fetchColumn();
        
        $password = 'Unknown';
        foreach (['password123', 'password', '12345678', 'test123', 'admin123'] as $test) {
            if (password_verify($test, $hash)) {
                $password = $test;
                break;
            }
        }
        
        echo $user['email'] . " / " . $password . " (" . ($user['business_name'] ?? 'No business') . ")\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}