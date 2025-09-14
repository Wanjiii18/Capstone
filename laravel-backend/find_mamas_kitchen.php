<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Finding the 'Mama's Kitchen' Account ===\n\n";

// Search for any account that might have "Mama's Kitchen" as name or business_name
// Maybe it's not exactly "Mama's Kitchen" but similar

echo "1. Searching for any karenderia with 'Mama' variations...\n";
$variations = ['mama', 'Mama', 'MAMA', 'mamas', "mama's", "Mama's"];

foreach ($variations as $search) {
    $karenderias = DB::table('karenderias')
        ->join('users', 'karenderias.owner_id', '=', 'users.id')
        ->select('karenderias.*', 'users.name as owner_name', 'users.email as owner_email')
        ->where('karenderias.name', 'like', "%{$search}%")
        ->orWhere('karenderias.business_name', 'like', "%{$search}%")
        ->get();
    
    if ($karenderias->count() > 0) {
        echo "Found matches for '{$search}':\n";
        foreach ($karenderias as $k) {
            echo "  ID: {$k->id} | Name: '{$k->name}' | Business: '{$k->business_name}' | Owner: {$k->owner_name} ({$k->owner_email})\n";
        }
    }
}

echo "\n2. Looking for ANY karenderia with 'Kitchen' in the name...\n";
$kitchens = DB::table('karenderias')
    ->join('users', 'karenderias.owner_id', '=', 'users.id')
    ->select('karenderias.*', 'users.name as owner_name', 'users.email as owner_email')
    ->where('karenderias.name', 'like', '%Kitchen%')
    ->orWhere('karenderias.business_name', 'like', '%Kitchen%')
    ->get();

if ($kitchens->count() > 0) {
    echo "Found matches with 'Kitchen':\n";
    foreach ($kitchens as $k) {
        echo "  ID: {$k->id} | Name: '{$k->name}' | Business: '{$k->business_name}' | Owner: {$k->owner_name} ({$k->owner_email})\n";
    }
} else {
    echo "No karenderias found with 'Kitchen' in the name.\n";
}

echo "\n3. Checking if there's a hardcoded fallback in the API...\n";

// Check all karenderias to see if any could be mistaken for "Mama's Kitchen"
$allKarenderias = DB::table('karenderias')
    ->join('users', 'karenderias.owner_id', '=', 'users.id')
    ->select('karenderias.*', 'users.name as owner_name', 'users.email as owner_email')
    ->get();

echo "All karenderias in database:\n";
foreach ($allKarenderias as $k) {
    echo "  ID: {$k->id} | Name: '{$k->name}' | Business: '{$k->business_name}' | Owner: {$k->owner_name} ({$k->owner_email})\n";
}

echo "\n4. Testing if the issue is in the frontend KarenderiaInfoService...\n";
echo "The problem might be:\n";
echo "- Frontend is calling a different API endpoint\n";
echo "- There's mock/fallback data in the frontend service\n";
echo "- The user_data in localStorage belongs to a different account\n";
echo "- The API endpoint is returning default data instead of user-specific data\n\n";

echo "=== DEBUG INSTRUCTIONS ===\n";
echo "In your browser console, please run these commands and share the results:\n\n";
echo "1. Check what's in localStorage:\n";
echo "   localStorage.getItem('user_data')\n\n";
echo "2. Check auth token:\n";
echo "   localStorage.getItem('auth_token')\n\n";
echo "3. Check what API endpoint is being called in Network tab:\n";
echo "   - Open DevTools > Network tab\n";
echo "   - Refresh the page\n";
echo "   - Look for calls to '/karenderias/' endpoints\n";
echo "   - Check what response they return\n\n";

echo "4. To test the actual API endpoint manually:\n";
echo "   In your browser console:\n";
echo "   fetch('/api/karenderias/my-karenderia', {\n";
echo "     headers: {\n";
echo "       'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),\n";
echo "       'Accept': 'application/json'\n";
echo "     }\n";
echo "   }).then(r => r.json()).then(console.log)\n";

?>