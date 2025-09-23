<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Karenderia;

echo "🔍 Checking all users and their karenderias:\n";
echo "============================================\n\n";

try {
    // Check all users
    $users = User::all();
    
    echo "👥 All users in database:\n";
    foreach ($users as $user) {
        echo "📧 Email: {$user->email}\n";
        echo "   Name: {$user->name}\n";
        echo "   ID: {$user->id}\n";
        echo "   Role: {$user->role}\n";
        
        // Check if this user has a karenderia
        $karenderia = Karenderia::where('owner_id', $user->id)->first();
        if ($karenderia) {
            echo "   🏪 Karenderia: {$karenderia->name} (Business: {$karenderia->business_name})\n";
            echo "   📊 Status: {$karenderia->status}\n";
        } else {
            echo "   🏪 Karenderia: None\n";
        }
        echo "   📅 Created: {$user->created_at}\n";
        echo "----------------------------\n\n";
    }
    
    // Check for specific emails
    echo "🎯 Checking specific users:\n";
    $targetEmails = ['test@gmail.com', 'try@test.com'];
    
    foreach ($targetEmails as $email) {
        $user = User::where('email', $email)->first();
        if ($user) {
            echo "✅ Found {$email}:\n";
            echo "   User ID: {$user->id}, Name: {$user->name}\n";
            
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            if ($karenderia) {
                echo "   Karenderia: {$karenderia->name} (ID: {$karenderia->id})\n";
            } else {
                echo "   No karenderia found\n";
            }
        } else {
            echo "❌ {$email} not found\n";
        }
    }
    
    echo "\n🔍 All karenderias and their owners:\n";
    $karenderias = Karenderia::all();
    foreach ($karenderias as $k) {
        $owner = User::find($k->owner_id);
        echo "🏪 {$k->name} (ID: {$k->id})\n";
        echo "   Owner: " . ($owner ? "{$owner->name} ({$owner->email})" : "Unknown (ID: {$k->owner_id})") . "\n";
        echo "   Status: {$k->status}\n";
        echo "----------------------------\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}