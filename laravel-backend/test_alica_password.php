<?php

$email = 'alica@gmail.com';
$password = 'password123';

try {
    // Test the exact login process
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=kaplato_db', 'root', 'root');

    // Check if user exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo 'User not found!' . PHP_EOL;
        exit;
    }

    echo 'User found:' . PHP_EOL;
    echo 'ID: ' . $user['id'] . PHP_EOL;
    echo 'Email: ' . $user['email'] . PHP_EOL;
    echo 'Role: ' . $user['role'] . PHP_EOL;
    echo 'Status: ' . $user['status'] . PHP_EOL;
    echo 'Password Hash: ' . $user['password'] . PHP_EOL;

    // Test password verification
    $passwordMatches = password_verify($password, $user['password']);
    echo 'Password matches: ' . ($passwordMatches ? 'YES' : 'NO') . PHP_EOL;

    // Check karenderia record
    $stmt = $pdo->prepare('SELECT * FROM karenderias WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($karenderia) {
        echo 'Karenderia found:' . PHP_EOL;
        echo 'Business Name: ' . $karenderia['business_name'] . PHP_EOL;
        echo 'Status: ' . $karenderia['status'] . PHP_EOL;
    } else {
        echo 'No karenderia record found!' . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}