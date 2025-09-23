<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING INCOMPLETE KARENDERIA OWNER REGISTRATIONS ===\n\n";

// Find karenderia owners without karenderia records
$incompleteOwners = App\Models\User::where('role', 'karenderia_owner')
    ->whereDoesntHave('karenderia')
    ->get();

echo "Found " . $incompleteOwners->count() . " karenderia owners without business records:\n\n";

foreach ($incompleteOwners as $user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Created: " . $user->created_at->format('Y-m-d H:i:s') . "\n";
    echo "Action: Converting to customer role (they can re-register as karenderia owner properly)\n";
    
    // Convert back to customer since they didn't complete karenderia registration
    $user->role = 'customer';
    $user->verified = true; // Allow them to login as customer
    $user->save();
    
    echo "✅ Converted to customer role\n";
    echo "-" . str_repeat("-", 60) . "\n";
}

echo "\n=== SUMMARY ===\n";
echo "Fixed " . $incompleteOwners->count() . " incomplete registrations\n";
echo "These users can now:\n";
echo "1. Login as customers normally\n";
echo "2. Re-register as karenderia owners using the proper form\n";

echo "\n=== CURRENT PENDING KARENDERIAS ===\n";
$pendingKarenderias = App\Models\Karenderia::where('status', 'pending')
    ->with('owner')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total pending: " . $pendingKarenderias->count() . "\n\n";

foreach ($pendingKarenderias as $karenderia) {
    echo "Business: " . $karenderia->business_name . "\n";
    echo "Owner: " . $karenderia->owner->name . " (" . $karenderia->owner->email . ")\n";
    echo "Status: " . $karenderia->status . "\n";
    echo "Created: " . $karenderia->created_at->format('Y-m-d H:i:s') . "\n";
    echo "-" . str_repeat("-", 60) . "\n";
}

echo "\nDone!\n";