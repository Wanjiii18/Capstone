<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== All Karenderia Owners in Database ===\n\n";

// Get all karenderia owners
$owners = App\Models\User::where('role', 'karenderia_owner')->get();

if ($owners->isEmpty()) {
    echo "❌ No karenderia owners found in database\n";
    exit(1);
}

echo "📋 Found {$owners->count()} karenderia owner(s):\n\n";

foreach ($owners as $owner) {
    echo "👤 Owner: {$owner->name}\n";
    echo "   - Email: {$owner->email}\n";
    echo "   - Username: {$owner->username}\n";
    
    // Check if this owner has a karenderia
    $karenderia = App\Models\Karenderia::where('owner_id', $owner->id)->first();
    
    if ($karenderia) {
        echo "   - Karenderia: {$karenderia->name}\n";
        echo "   - Status: {$karenderia->status}\n";
    } else {
        echo "   - Karenderia: None\n";
    }
    echo "\n";
}
