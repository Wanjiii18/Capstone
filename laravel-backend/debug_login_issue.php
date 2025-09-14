<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debugging Login Issue ===\n\n";

// Check tabada account details
$email = 'tabada@gmail.com';
$user = DB::table('users')->where('email', $email)->first();

if ($user) {
    echo "User details:\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Password Hash: " . substr($user->password, 0, 20) . "...\n";
    echo "  Created: {$user->created_at}\n\n";
    
    // Test different common passwords
    $testPasswords = ['password123', 'password', 'tabada123', 'tabada'];
    
    foreach ($testPasswords as $testPassword) {
        if (password_verify($testPassword, $user->password)) {
            echo "✅ Found correct password: {$testPassword}\n";
            break;
        } else {
            echo "❌ Password '{$testPassword}' doesn't match\n";
        }
    }
}

// Look for any karenderias with "Mama" in the name (including deleted/hidden ones)
echo "\n=== Searching ALL karenderias for 'Mama' ===\n";

$allKarenderias = DB::table('karenderias')->get();
$foundMama = false;

foreach ($allKarenderias as $k) {
    if (stripos($k->name, 'mama') !== false || stripos($k->business_name, 'mama') !== false) {
        $owner = DB::table('users')->where('id', $k->owner_id)->first();
        echo "Found 'Mama' karenderia:\n";
        echo "  ID: {$k->id}\n";
        echo "  Name: '{$k->name}'\n";
        echo "  Business Name: '{$k->business_name}'\n";
        echo "  Owner: {$owner->name} ({$owner->email})\n";
        echo "  Status: {$k->status}\n\n";
        $foundMama = true;
    }
}

if (!$foundMama) {
    echo "No karenderias found with 'Mama' in database\n";
}

// Check if there's any mock/test data in the frontend
echo "\n=== Possible Frontend Issues ===\n";
echo "Since 'Mama's Kitchen' is not in the database but shows in frontend:\n";
echo "1. Frontend might be using cached/mock data\n";
echo "2. API call might be failing and using fallback\n";
echo "3. Different environment/server data\n";
echo "4. Browser localStorage might have old data\n\n";

echo "To check frontend:\n";
echo "1. Clear browser cache completely\n";
echo "2. Check localStorage in DevTools: localStorage.clear()\n";
echo "3. Check if API call to /karenderias/my-karenderia actually succeeds\n";
echo "4. Check Network tab for 401/404 errors\n";

?>