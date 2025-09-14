<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\UserController;
use App\Models\User;

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Profile Business Information Fix ===\n\n";

// Test user credentials
$testEmail = 'joseph2@gmail.com';
$testPassword = 'password123';

echo "1. Testing User Profile API (/api/user/profile)\n";
echo "   Email: $testEmail\n";

// Find the user
$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "   ❌ User not found!\n";
    exit(1);
}

echo "   ✅ User found: {$user->displayName} (Role: {$user->role})\n";

// Test UserController getProfile
try {
    $userController = new UserController();
    
    // Create a mock request with the user
    $request = new Request();
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $userProfile = $userController->getProfile($request);
    $userProfileData = $userProfile->getData(true);
    
    echo "   User Profile Data:\n";
    echo "   - Display Name: " . ($userProfileData['displayName'] ?? 'null') . "\n";
    echo "   - Email: " . ($userProfileData['email'] ?? 'null') . "\n";
    echo "   - Phone: " . ($userProfileData['phoneNumber'] ?? 'null') . "\n";
    echo "   - Role: " . ($userProfileData['role'] ?? 'null') . "\n";
    echo "   - Address: " . ($userProfileData['address'] ?? 'null') . "\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Error testing user profile: " . $e->getMessage() . "\n\n";
}

echo "2. Testing Karenderia API (/api/karenderias/my-karenderia)\n";

if ($user->role === 'karenderia_owner') {
    try {
        $karenderiaController = new KarenderiaController();
        
        // Create a mock request with the user
        $request = new Request();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        $karenderiaResponse = $karenderiaController->myKarenderia($request);
        $karenderiaData = $karenderiaResponse->getData(true);
        
        echo "   Karenderia Business Data:\n";
        echo "   - Business Name: " . ($karenderiaData['name'] ?? 'null') . "\n";
        echo "   - Phone Number: " . ($karenderiaData['phoneNumber'] ?? 'null') . "\n";
        echo "   - Email: " . ($karenderiaData['email'] ?? 'null') . "\n";
        echo "   - Address: " . ($karenderiaData['address'] ?? 'null') . "\n";
        echo "   - Description: " . ($karenderiaData['description'] ?? 'null') . "\n";
        echo "   - Status: " . ($karenderiaData['status'] ?? 'null') . "\n\n";
        
        // Check if business data matches expected values
        $expectedBusinessName = 'JOSEPH 2 KITHCEN';
        $expectedPhone = '+639959169306';
        
        if ($karenderiaData['name'] === $expectedBusinessName) {
            echo "   ✅ Business name is correct: {$karenderiaData['name']}\n";
        } else {
            echo "   ❌ Business name mismatch. Expected: $expectedBusinessName, Got: " . ($karenderiaData['name'] ?? 'null') . "\n";
        }
        
        if ($karenderiaData['phoneNumber'] === $expectedPhone) {
            echo "   ✅ Phone number is correct: {$karenderiaData['phoneNumber']}\n";
        } else {
            echo "   ❌ Phone number mismatch. Expected: $expectedPhone, Got: " . ($karenderiaData['phoneNumber'] ?? 'null') . "\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error testing karenderia data: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  User is not a karenderia owner (Role: {$user->role})\n";
}

echo "\n3. Solution Summary:\n";
echo "   - User Profile API returns personal user data (displayName, email, etc.)\n";
echo "   - Karenderia API returns business data (business name, phone, description)\n";
echo "   - Profile page should now show correct business data for karenderia owners\n";
echo "   - Business section will display karenderia data instead of personal user data\n\n";

echo "=== Test Complete ===\n";