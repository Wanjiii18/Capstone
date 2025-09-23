<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING PENDING KARENDERIA APPLICATIONS ===\n\n";

$pendingKarenderias = App\Models\Karenderia::where('status', 'pending')
    ->with('owner')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total pending applications: " . $pendingKarenderias->count() . "\n\n";

if ($pendingKarenderias->count() > 0) {
    echo "Pending Applications:\n";
    echo "-" . str_repeat("-", 80) . "\n";
    
    foreach ($pendingKarenderias as $karenderia) {
        echo "ID: " . $karenderia->id . "\n";
        echo "Business Name: " . $karenderia->business_name . "\n";
        echo "Owner: " . $karenderia->owner->name . "\n";
        echo "Email: " . $karenderia->owner->email . "\n";
        echo "Address: " . $karenderia->address . "\n";
        echo "Status: " . $karenderia->status . "\n";
        echo "Created: " . $karenderia->created_at->format('Y-m-d H:i:s') . "\n";
        echo "-" . str_repeat("-", 80) . "\n";
    }
} else {
    echo "No pending applications found.\n";
}

echo "\n=== CHECKING RECENT USER REGISTRATIONS ===\n\n";

$recentUsers = App\Models\User::where('role', 'karenderia_owner')
    ->where('created_at', '>=', now()->subDays(7))
    ->orderBy('created_at', 'desc')
    ->get();

echo "Recent karenderia owner registrations (last 7 days): " . $recentUsers->count() . "\n\n";

if ($recentUsers->count() > 0) {
    echo "Recent Registrations:\n";
    echo "-" . str_repeat("-", 60) . "\n";
    
    foreach ($recentUsers as $user) {
        echo "ID: " . $user->id . "\n";
        echo "Name: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Role: " . $user->role . "\n";
        echo "Created: " . $user->created_at->format('Y-m-d H:i:s') . "\n";
        echo "Has Karenderia: " . ($user->karenderia ? 'Yes' : 'No') . "\n";
        if ($user->karenderia) {
            echo "Karenderia Status: " . $user->karenderia->status . "\n";
        }
        echo "-" . str_repeat("-", 60) . "\n";
    }
}

echo "\nDone.\n";