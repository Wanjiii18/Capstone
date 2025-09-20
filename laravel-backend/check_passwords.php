<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking User Passwords ===\n\n";

$users = [
    'owner@kaplato.com',
    'alica@kaplato.com'
];

foreach($users as $email) {
    $user = \App\Models\User::where('email', $email)->first();
    
    if ($user) {
        echo "✅ User: {$user->name} ({$email})\n";
        echo "   Role: {$user->role}\n";
        
        // Test common passwords
        $passwords = ['owner123', 'password123', 'alica123', 'admin123'];
        $foundPassword = false;
        
        foreach($passwords as $password) {
            if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                echo "   ✅ Password: {$password}\n";
                $foundPassword = true;
                break;
            }
        }
        
        if (!$foundPassword) {
            echo "   ❌ None of the common passwords work\n";
        }
        
    } else {
        echo "❌ User {$email} not found\n";
    }
    echo "---\n";
}