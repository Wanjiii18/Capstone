<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\User;
use App\Models\Karenderia;
use App\Models\MenuItem;

echo "=== Daily Menu Access Check ===\n\n";

// Check all users and their roles
echo "All Users:\n";
$users = User::all();
foreach ($users as $user) {
    echo "- {$user->email} (Role: {$user->role}, ID: {$user->id})\n";
}

echo "\nAll Karenderias:\n";
$karenderias = Karenderia::with('owner')->get();
foreach ($karenderias as $karenderia) {
    $ownerEmail = $karenderia->owner ? $karenderia->owner->email : 'No Owner';
    echo "- {$karenderia->name} (Owner: {$ownerEmail}, Owner ID: {$karenderia->owner_id})\n";
}

echo "\nUser-Karenderia Mapping:\n";
foreach ($users as $user) {
    $karenderia = Karenderia::where('owner_id', $user->id)->first();
    if ($karenderia) {
        echo "- {$user->email} owns '{$karenderia->name}'\n";
        
        // Check menu items for this karenderia
        $menuItemCount = MenuItem::where('karenderia_id', $karenderia->id)->count();
        echo "  Menu items: {$menuItemCount}\n";
    } else {
        echo "- {$user->email} has no karenderia\n";
    }
}

echo "\nUsers who can access Daily Menu Management:\n";
$karenderiaOwners = User::whereHas('karenderias')->get();
foreach ($karenderiaOwners as $owner) {
    echo "- {$owner->email}\n";
}
