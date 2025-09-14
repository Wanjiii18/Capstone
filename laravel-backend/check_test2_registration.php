<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking test2@kaplato.com registration...\n\n";

// Find the user
$user = DB::table('users')->where('email', 'test2@kaplato.com')->first();
if (!$user) {
    echo "❌ User test2@kaplato.com not found!\n";
    exit;
}

echo "✅ User Found:\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n\n";

// Find their karenderia
$karenderia = DB::table('karenderias')->where('owner_id', $user->id)->first();
if (!$karenderia) {
    echo "❌ No karenderia found for this user!\n";
    exit;
}

echo "📋 Karenderia Registration Data:\n";
echo "ID: {$karenderia->id}\n";
echo "Name (account): {$karenderia->name}\n";
echo "Business Name: " . ($karenderia->business_name ?? 'NULL') . "\n";
echo "Description: " . ($karenderia->description ?? 'NULL') . "\n";
echo "Address: " . ($karenderia->address ?? 'NULL') . "\n";
echo "Phone: " . ($karenderia->phone ?? 'NULL') . "\n";
echo "Email: " . ($karenderia->email ?? 'NULL') . "\n";

// Check what the API would return
echo "\n🔍 API Response Simulation:\n";
$apiData = [
    'id' => $karenderia->id,
    'name' => $karenderia->name,
    'business_name' => $karenderia->business_name,
    'description' => $karenderia->description,
];

echo "API name field: {$apiData['name']}\n";
echo "API business_name field: " . ($apiData['business_name'] ?? 'NULL') . "\n";

// Frontend display logic simulation
$displayName = $apiData['business_name'] ?? $apiData['name'] ?? 'Unknown';
echo "\n🎯 Frontend Display Result: '{$displayName}'\n";

if ($displayName === "mama's kitchen") {
    echo "❌ PROBLEM: Still showing 'mama's kitchen'!\n";
    echo "🔍 Need to check why business_name is not being saved correctly during registration.\n";
} else {
    echo "✅ SUCCESS: Showing correct business name!\n";
}

?>