<?php

require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Karenderia Owners and Business Names ===\n\n";

$users = \App\Models\User::where('role', 'karenderia_owner')->get();

foreach($users as $user) {
    echo "USER: {$user->name} ({$user->email})\n";
    
    $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
    
    if($karenderia) {
        echo "  Business Name: {$karenderia->business_name}\n";
        echo "  Display Name: {$karenderia->name}\n";
        echo "  Status: {$karenderia->status}\n";
        echo "  Address: {$karenderia->address}\n";
    } else {
        echo "  No karenderia business found\n";
    }
    echo "---\n";
}

echo "\n=== Test Login Suggestions ===\n";
echo "Try logging in with these accounts to see their business names:\n";
foreach($users as $user) {
    echo "- {$user->email} (likely password: password123 or owner123)\n";
}