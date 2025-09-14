<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Searching for 'Mama's Kitchen' ===\n\n";

// Search for mama's kitchen in karenderias
$mamas = DB::table('karenderias')
    ->where('name', 'like', '%mama%')
    ->orWhere('business_name', 'like', '%mama%')
    ->get();

if ($mamas->count() > 0) {
    echo "Found karenderias with 'mama' in the name:\n";
    foreach ($mamas as $mama) {
        $user = DB::table('users')->where('id', $mama->owner_id)->first();
        echo "  ID: {$mama->id}\n";
        echo "  Name: {$mama->name}\n";
        echo "  Business Name: {$mama->business_name}\n";
        echo "  Owner: {$user->name} ({$user->email})\n";
        echo "  Status: {$mama->status}\n";
        echo "  ---\n";
    }
} else {
    echo "No karenderias found with 'mama' in the name.\n";
}

// Check all karenderias to see their names
echo "\n=== All Karenderias in Database ===\n";
$allKarenderias = DB::table('karenderias')
    ->join('users', 'karenderias.owner_id', '=', 'users.id')
    ->select('karenderias.*', 'users.name as owner_name', 'users.email as owner_email')
    ->orderBy('karenderias.id')
    ->get();

foreach ($allKarenderias as $k) {
    echo "ID: {$k->id} | Name: '{$k->name}' | Business: '{$k->business_name}' | Owner: {$k->owner_name}\n";
}

// Check if there's any default/hardcoded values in the frontend
echo "\n=== Analysis ===\n";
echo "If you're seeing 'Mama's Kitchen', it could be:\n";
echo "1. Cached data in the browser\n";
echo "2. Default/fallback value in the frontend\n";
echo "3. Different user logged in\n";
echo "4. Frontend not calling the correct API endpoint\n";

?>