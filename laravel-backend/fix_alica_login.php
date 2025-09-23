<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ALICA LOGIN ISSUES ===\n\n";

$user = App\Models\User::where('email', 'alica@gmail.com')->with('karenderia')->first();

if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

echo "Fixing user: {$user->name} ({$user->email})\n\n";

// Fix 1: Reset password to a known value
$user->password = Illuminate\Support\Facades\Hash::make('password123');
$user->verified = true; // Make sure user is verified
$user->save();

echo "✅ Password reset to: password123\n";
echo "✅ User verified: true\n";

// Fix 2: Fix karenderia approval data
if ($user->karenderia) {
    $karenderia = $user->karenderia;
    
    if ($karenderia->status === 'approved' && !$karenderia->approved_at) {
        $karenderia->approved_at = now();
        $karenderia->save();
        echo "✅ Fixed approved_at timestamp\n";
    }
    
    echo "Karenderia status: {$karenderia->status}\n";
    echo "Approved at: {$karenderia->approved_at}\n";
}

echo "\n=== TESTING LOGIN ===\n";

// Test the login process
if (Illuminate\Support\Facades\Auth::attempt(['email' => 'alica@gmail.com', 'password' => 'password123'])) {
    $authUser = Illuminate\Support\Facades\Auth::user();
    echo "✅ Login successful!\n";
    echo "User: {$authUser->name}\n";
    echo "Role: {$authUser->role}\n";
    
    if ($authUser->karenderia) {
        echo "Business: {$authUser->karenderia->business_name}\n";
        echo "Status: {$authUser->karenderia->status}\n";
    }
    
    Illuminate\Support\Facades\Auth::logout();
} else {
    echo "❌ Login still failed\n";
}

echo "\n✅ User should now be able to login with:\n";
echo "Email: alica@gmail.com\n";
echo "Password: password123\n";