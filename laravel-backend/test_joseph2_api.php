<?php
// Test what data is returned for joseph2@gmail.com user profile
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING JOSEPH2 USER PROFILE API ===\n\n";

try {
    // Find joseph2 user
    $user = \App\Models\User::where('email', 'joseph2@gmail.com')->first();
    if (!$user) {
        echo "❌ User joseph2@gmail.com not found!\n";
        exit;
    }

    echo "✅ User found: {$user->name} ({$user->email})\n";
    echo "Role: {$user->role}\n\n";

    // Check if user has any tokens (for API testing)
    $tokens = $user->tokens()->get();
    echo "=== USER TOKENS ===\n";
    foreach($tokens as $token) {
        echo "Token ID: {$token->id} | Name: {$token->name}\n";
    }
    
    if ($tokens->count() > 0) {
        $latestToken = $tokens->last();
        echo "\nUsing latest token: {$latestToken->id}\n";
        
        // Simulate API calls that profile might make
        echo "\n=== SIMULATING PROFILE API CALLS ===\n";
        
        // Set authenticated user
        auth()->login($user);
        
        // Test auth user endpoint
        echo "1. Auth User Data:\n";
        echo "   ID: {$user->id}\n";
        echo "   Name: {$user->name}\n";
        echo "   Email: {$user->email}\n";
        echo "   Role: {$user->role}\n\n";
        
        // Test karenderia data if user is karenderia owner
        if ($user->role === 'karenderia_owner') {
            echo "2. Karenderia Owner Data:\n";
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            if ($karenderia) {
                echo "   Karenderia ID: {$karenderia->id}\n";
                echo "   Name: {$karenderia->name}\n";
                echo "   Business Name: {$karenderia->business_name}\n";
                echo "   Description: {$karenderia->description}\n";
                echo "   Address: {$karenderia->address}\n";
                echo "   Phone: {$karenderia->phone}\n";
                echo "   Email: {$karenderia->email}\n";
            } else {
                echo "   No karenderia found for this user\n";
            }
        }
        
    } else {
        echo "No tokens found - user needs to login\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>