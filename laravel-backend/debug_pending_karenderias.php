<?php

/**
 * Debug Pending Karenderias Script
 * Check what data is actually in the database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Pending Karenderias ===\n\n";

// Get all karenderias with status = pending
$pendingKarenderias = DB::table('karenderias')
    ->where('status', 'pending')
    ->get();

echo "Total Pending Karenderias: " . $pendingKarenderias->count() . "\n\n";

if ($pendingKarenderias->count() > 0) {
    echo "=== Pending Karenderias Details ===\n";
    foreach ($pendingKarenderias as $karenderia) {
        echo "------------------------------------\n";
        echo "ID: {$karenderia->id}\n";
        echo "Name: {$karenderia->name}\n";
        echo "Business Name: {$karenderia->business_name}\n";
        echo "Owner ID: {$karenderia->owner_id}\n";
        echo "Status: {$karenderia->status}\n";
        echo "Email: " . ($karenderia->email ?? 'N/A') . "\n";
        echo "Created At: {$karenderia->created_at}\n";
        
        // Check if owner exists
        $owner = DB::table('users')->where('id', $karenderia->owner_id)->first();
        if ($owner) {
            echo "Owner Name: {$owner->name}\n";
            echo "Owner Email: {$owner->email}\n";
            echo "Owner Role: {$owner->role}\n";
        } else {
            echo "⚠️ WARNING: Owner not found!\n";
        }
        echo "\n";
    }
} else {
    echo "❌ No pending karenderias found!\n\n";
    
    echo "Let's check all karenderias...\n\n";
    $allKarenderias = DB::table('karenderias')->get();
    echo "Total Karenderias: " . $allKarenderias->count() . "\n\n";
    
    if ($allKarenderias->count() > 0) {
        echo "=== All Karenderias by Status ===\n";
        $statusGroups = [];
        foreach ($allKarenderias as $k) {
            $status = $k->status ?? 'null';
            if (!isset($statusGroups[$status])) {
                $statusGroups[$status] = [];
            }
            $statusGroups[$status][] = $k;
        }
        
        foreach ($statusGroups as $status => $karenderias) {
            echo "\nStatus: {$status} (" . count($karenderias) . " entries)\n";
            foreach ($karenderias as $k) {
                echo "  - ID {$k->id}: {$k->business_name} (Created: {$k->created_at})\n";
            }
        }
    }
}

echo "\n=== Recent Users (Last 5) ===\n";
$recentUsers = DB::table('users')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recentUsers as $user) {
    echo "ID: {$user->id} | {$user->name} | {$user->email} | Role: {$user->role} | Created: {$user->created_at}\n";
}

echo "\n=== Checking User-Karenderia Relationships ===\n";
$karenderiaOwners = DB::table('users')
    ->where('role', 'karenderia_owner')
    ->get();

foreach ($karenderiaOwners as $owner) {
    $karenderiaCount = DB::table('karenderias')
        ->where('owner_id', $owner->id)
        ->count();
    
    if ($karenderiaCount == 0) {
        echo "⚠️ WARNING: User {$owner->id} ({$owner->email}) is a karenderia_owner but has NO karenderia!\n";
    } else {
        $karenderia = DB::table('karenderias')
            ->where('owner_id', $owner->id)
            ->first();
        echo "✓ User {$owner->id} ({$owner->email}) has karenderia: {$karenderia->business_name} (Status: {$karenderia->status})\n";
    }
}

echo "\n=== Summary ===\n";
echo "If you just registered and don't see it above, there may be an issue with the registration process.\n";
echo "Check the Laravel log for any errors: storage/logs/laravel.log\n";
