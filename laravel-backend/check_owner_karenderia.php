<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking owner@kaplato.com Karenderia ===\n\n";

// Check if the user exists
$email = 'owner@kaplato.com';
$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found with email: {$email}\n";
    exit(1);
}

echo "✅ User found:\n";
echo "   - ID: {$user->id}\n";
echo "   - Name: {$user->name}\n";
echo "   - Email: {$user->email}\n";
echo "   - Role: {$user->role}\n";
echo "   - Password Hash: " . substr($user->password, 0, 20) . "...\n\n";

// Check if this user has a karenderia
$karenderia = App\Models\Karenderia::where('owner_id', $user->id)->first();

if (!$karenderia) {
    echo "❌ No karenderia found for this owner\n\n";
    
    // Show all karenderias and their owners
    echo "📋 All karenderias in the database:\n";
    $karenderias = App\Models\Karenderia::with('owner')->get();
    foreach ($karenderias as $k) {
        echo "   - {$k->name} (Owner: {$k->owner->name} - {$k->owner->email})\n";
    }
} else {
    echo "✅ Karenderia found:\n";
    echo "   - ID: {$karenderia->id}\n";
    echo "   - Name: {$karenderia->name}\n";
    echo "   - Business Name: {$karenderia->business_name}\n";
    echo "   - Status: {$karenderia->status}\n";
    echo "   - Address: {$karenderia->address}\n";
}
