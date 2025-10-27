<?php

require_once __DIR__.'/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=kaplato_db', 'root', 'root');

echo "========================================\n";
echo "PENDING KARENDERIAS IN ADMIN PANEL\n";
echo "========================================\n\n";

// Get pending karenderias with owner info
$stmt = $pdo->query("
    SELECT 
        k.id,
        k.business_name,
        k.address,
        k.city,
        k.province,
        k.status,
        k.created_at,
        u.id as owner_id,
        u.name as owner_name,
        u.email as owner_email,
        u.role as owner_role
    FROM karenderias k
    JOIN users u ON k.owner_id = u.id
    WHERE k.status = 'pending'
    ORDER BY k.created_at DESC
");

$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($pending) > 0) {
    echo "✅ Found " . count($pending) . " pending karenderia(s):\n\n";
    
    foreach ($pending as $i => $k) {
        echo "-----------------------------------\n";
        echo "Pending Application #" . ($i + 1) . "\n";
        echo "-----------------------------------\n";
        echo "Karenderia ID: {$k['id']}\n";
        echo "Business Name: {$k['business_name']}\n";
        echo "Address: {$k['address']}, {$k['city']}, {$k['province']}\n";
        echo "Status: {$k['status']}\n";
        echo "Applied: {$k['created_at']}\n\n";
        echo "Owner Information:\n";
        echo "  Owner ID: {$k['owner_id']}\n";
        echo "  Name: {$k['owner_name']}\n";
        echo "  Email: {$k['owner_email']}\n";
        echo "  Role: {$k['owner_role']}\n\n";
    }
    
    echo "========================================\n";
    echo "These should appear in the admin panel at:\n";
    echo "http://localhost:8000/admin/pending\n";
    echo "or\n";
    echo "http://192.168.1.17:8000/admin/pending\n";
    echo "========================================\n";
    
} else {
    echo "❌ No pending karenderias found!\n";
}
