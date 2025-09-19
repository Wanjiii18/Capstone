<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG MENU ITEMS ===\n\n";

// Check all karenderias
echo "1. ALL KARENDERIAS:\n";
$karenderias = \App\Models\Karenderia::all();
foreach ($karenderias as $karenderia) {
    echo "ID: {$karenderia->id}, Name: {$karenderia->name}, Owner ID: {$karenderia->owner_id}\n";
}

echo "\n2. ALL USERS:\n";
$users = \App\Models\User::where('role', 'karenderia_owner')->get();
foreach ($users as $user) {
    echo "ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
}

echo "\n3. ALL MENU ITEMS:\n";
$menuItems = \App\Models\MenuItem::with('karenderia')->get();
foreach ($menuItems as $item) {
    $karenderiaName = $item->karenderia ? $item->karenderia->name : 'NO KARENDERIA';
    echo "ID: {$item->id}, Name: {$item->name}, Karenderia ID: {$item->karenderia_id}, Karenderia: {$karenderiaName}\n";
}

echo "\n4. USER-KARENDERIA MAPPING:\n";
foreach ($users as $user) {
    $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
    if ($karenderia) {
        $menuCount = \App\Models\MenuItem::where('karenderia_id', $karenderia->id)->count();
        echo "User: {$user->email} -> Karenderia: {$karenderia->name} (ID: {$karenderia->id}) -> Menu Items: {$menuCount}\n";
    } else {
        echo "User: {$user->email} -> NO KARENDERIA FOUND\n";
    }
}

echo "\n=== END DEBUG ===\n";