<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING LOGIN ISSUE FOR alica@gmail.com ===\n\n";

$email = 'alica@gmail.com';
$user = App\Models\User::where('email', $email)->with('karenderia')->first();

if (!$user) {
    echo "❌ User not found with email: $email\n";
    exit(1);
}

echo "✅ User found:\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n";
echo "Verified: " . ($user->verified ? 'Yes' : 'No') . "\n";
echo "Created: {$user->created_at}\n\n";

if ($user->karenderia) {
    echo "✅ Karenderia found:\n";
    echo "ID: {$user->karenderia->id}\n";
    echo "Name: {$user->karenderia->name}\n";
    echo "Business Name: {$user->karenderia->business_name}\n";
    echo "Status: {$user->karenderia->status}\n";
    echo "Approved At: " . ($user->karenderia->approved_at ?? 'Not approved') . "\n\n";
} else {
    echo "❌ No karenderia found for this user\n\n";
}

echo "=== TESTING LOGIN LOGIC ===\n";

// Test password verification (assuming default password)
$testPasswords = ['password', 'password123', 'admin123', 'alica123'];

foreach ($testPasswords as $password) {
    if (Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        echo "✅ Password '$password' is correct\n";
        break;
    } else {
        echo "❌ Password '$password' is incorrect\n";
    }
}

echo "\n=== SIMULATING LOGIN PROCESS ===\n";

try {
    // Check if login would work
    if ($user->role === 'karenderia_owner') {
        if (!$user->karenderia) {
            echo "❌ Login would fail: No karenderia application found\n";
        } elseif ($user->karenderia->status === 'pending') {
            echo "❌ Login would be blocked: Karenderia application is pending approval\n";
            echo "Status: {$user->karenderia->status}\n";
            echo "Business: {$user->karenderia->business_name}\n";
        } elseif ($user->karenderia->status === 'rejected') {
            echo "❌ Login would be blocked: Karenderia application was rejected\n";
            echo "Status: {$user->karenderia->status}\n";
            if ($user->karenderia->rejection_reason) {
                echo "Reason: {$user->karenderia->rejection_reason}\n";
            }
        } elseif ($user->karenderia->status === 'approved') {
            echo "✅ Login should work: Karenderia is approved\n";
            echo "Status: {$user->karenderia->status}\n";
            echo "Business: {$user->karenderia->business_name}\n";
            echo "Approved: {$user->karenderia->approved_at}\n";
        } else {
            echo "❓ Unknown karenderia status: {$user->karenderia->status}\n";
        }
    } else {
        echo "✅ Login should work: Regular user (role: {$user->role})\n";
    }
} catch (Exception $e) {
    echo "❌ Error in login simulation: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATION ===\n";
if ($user->role === 'karenderia_owner' && $user->karenderia && $user->karenderia->status === 'approved') {
    echo "The user should be able to login. Check:\n";
    echo "1. Frontend is sending correct credentials\n";
    echo "2. Backend server is running\n";
    echo "3. API endpoint is accessible\n";
    echo "4. Check browser network tab for actual error\n";
} else {
    echo "Login is correctly blocked due to approval status.\n";
}