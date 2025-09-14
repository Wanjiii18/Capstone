<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\KarenderiaController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Karenderia API with Proper Authentication ===\n\n";

$testEmail = 'joseph2@gmail.com';

// Find the user
$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->displayName} (ID: {$user->id}, Role: {$user->role})\n\n";

// Test the API directly with proper authentication
try {
    // Manually authenticate the user
    Auth::setUser($user);
    
    $karenderiaController = new KarenderiaController();
    
    // Create a request
    $request = new Request();
    
    $response = $karenderiaController->myKarenderia($request);
    
    echo "Response Status Code: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n\n";
    
    // Parse the JSON response
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        $karenderiaData = $responseData['data'];
        
        echo "✅ API call successful!\n";
        echo "Karenderia Business Data:\n";
        echo "- Business Name: " . ($karenderiaData['name'] ?? 'null') . "\n";
        echo "- Phone Number: " . ($karenderiaData['phone'] ?? 'null') . "\n";
        echo "- Email: " . ($karenderiaData['email'] ?? 'null') . "\n";
        echo "- Address: " . ($karenderiaData['address'] ?? 'null') . "\n";
        echo "- Description: " . ($karenderiaData['description'] ?? 'null') . "\n";
        echo "- Status: " . ($karenderiaData['status'] ?? 'null') . "\n\n";
        
        // Verify the expected data
        $expectedName = 'JOSEPH 2 KITHCEN';
        $expectedPhone = '+639959169306';
        
        if ($karenderiaData['name'] === $expectedName) {
            echo "✅ Business name is correct: {$karenderiaData['name']}\n";
        } else {
            echo "❌ Business name mismatch. Expected: $expectedName, Got: " . ($karenderiaData['name'] ?? 'null') . "\n";
        }
        
        if ($karenderiaData['phone'] === $expectedPhone) {
            echo "✅ Phone number is correct: {$karenderiaData['phone']}\n";
        } else {
            echo "❌ Phone number mismatch. Expected: $expectedPhone, Got: " . ($karenderiaData['phone'] ?? 'null') . "\n";
        }
    } else {
        echo "❌ API call failed: " . $responseData['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";