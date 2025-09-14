<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Finding Joseph2's Karenderia ===\n\n";

// Find the user
$user = User::where('email', 'joseph2@gmail.com')->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->displayName} (ID: {$user->id})\n\n";

// Check karenderias with owner_id
$karenderias = \DB::table('karenderias')->where('owner_id', $user->id)->get();

if ($karenderias->isEmpty()) {
    echo "❌ No karenderia found for owner_id: {$user->id}\n\n";
    
    // Check all karenderias with email matching the user's email
    echo "Checking karenderias by email...\n";
    $karenderiasByEmail = \DB::table('karenderias')->where('email', $user->email)->get();
    
    if ($karenderiasByEmail->isEmpty()) {
        echo "❌ No karenderia found with email: {$user->email}\n";
        
        // Show all karenderias to see what's available
        echo "\nAll karenderias in database:\n";
        $allKarenderias = \DB::table('karenderias')->get();
        foreach ($allKarenderias as $k) {
            echo "- ID: {$k->id}, Name: {$k->name}, Email: {$k->email}, Owner_ID: {$k->owner_id}\n";
        }
        
    } else {
        echo "✅ Found karenderia by email:\n";
        foreach ($karenderiasByEmail as $k) {
            echo "- ID: {$k->id}\n";
            echo "- Name: {$k->name}\n";
            echo "- Email: {$k->email}\n";
            echo "- Phone: {$k->phone}\n";
            echo "- Owner_ID: {$k->owner_id}\n";
            echo "- Description: {$k->description}\n";
        }
    }
    
} else {
    echo "✅ Found karenderia:\n";
    foreach ($karenderias as $k) {
        echo "- ID: {$k->id}\n";
        echo "- Name: {$k->name}\n";
        echo "- Email: {$k->email}\n";
        echo "- Phone: {$k->phone}\n";
        echo "- Description: {$k->description}\n";
        echo "- Address: {$k->address}\n";
        echo "- Status: {$k->status}\n";
    }
}

echo "\n=== Search Complete ===\n";