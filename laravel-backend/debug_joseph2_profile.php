<?php
// Test joseph2@gmail.com profile data
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING JOSEPH2 PROFILE DATA ===\n\n";

try {
    // Find joseph2 user
    $user = \App\Models\User::where('email', 'joseph2@gmail.com')->first();
    if (!$user) {
        echo "❌ User joseph2@gmail.com not found!\n";
        exit;
    }

    echo "✅ User found:\n";
    echo "   ID: {$user->id}\n";
    echo "   Email: {$user->email}\n";
    echo "   Name: {$user->name}\n";
    echo "   Role: {$user->role}\n\n";

    // Check if user has karenderia
    $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
    if (!$karenderia) {
        echo "❌ No karenderia found for this user!\n";
        exit;
    }

    echo "✅ Karenderia found:\n";
    echo "   ID: {$karenderia->id}\n";
    echo "   Name: {$karenderia->name}\n";
    echo "   Business Name: {$karenderia->business_name}\n";
    echo "   Description: {$karenderia->description}\n";
    echo "   Address: {$karenderia->address}\n";
    echo "   Phone: {$karenderia->phone}\n";
    echo "   Email: {$karenderia->email}\n";
    echo "   Owner ID: {$karenderia->owner_id}\n\n";

    // Check if there are multiple karenderias with similar names
    echo "=== CHECKING FOR NAME CONFLICTS ===\n";
    $similarKarenderias = \App\Models\Karenderia::where('name', 'like', '%kitchen%')->get();
    
    foreach($similarKarenderias as $k) {
        $owner = \App\Models\User::find($k->owner_id);
        echo "Karenderia: {$k->name} | Owner: " . ($owner ? $owner->email : 'Unknown') . " | ID: {$k->id}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>