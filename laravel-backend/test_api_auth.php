<?php

require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing API Authentication ===\n\n";

// Simulate the login process and get a token
$authController = new \App\Http\Controllers\AuthController();

// Test login for owner@kaplato.com
echo "1. Testing login for owner@kaplato.com:\n";

// Try common passwords
$passwords = ['password', 'password123', 'admin123', 'owner123'];
$loginSuccess = false;
$token = null;

foreach ($passwords as $pwd) {
    try {
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'email' => 'owner@kaplato.com',
            'password' => $pwd
        ]);
        
        $response = $authController->login($request);
        $responseData = json_decode($response->getContent(), true);
        
        if (isset($responseData['access_token'])) {
            echo "✅ Login successful with password '$pwd'! Token: " . substr($responseData['access_token'], 0, 20) . "...\n";
            $token = $responseData['access_token'];
            $loginSuccess = true;
            break;
        }
    } catch (\Exception $e) {
        // Continue trying
    }
}

if ($loginSuccess) {
    // Test the karenderia endpoint with this token
    echo "\n2. Testing /api/karenderias/my-karenderia with token:\n";
    
    // Simulate authentication
    \Illuminate\Support\Facades\Auth::loginUsingId($responseData['user']['id']);
    
    $karenderiaController = new \App\Http\Controllers\KarenderiaController();
    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function() use ($responseData) {
        return \App\Models\User::find($responseData['user']['id']);
    });
    
    $karenderiaResponse = $karenderiaController->myKarenderia($request);
    $karenderiaData = json_decode($karenderiaResponse->getContent(), true);
    
    echo "API Response: " . json_encode($karenderiaData, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ All login attempts failed for owner@kaplato.com\n";
}

echo "\n3. Testing login for rosa.karenderia@email.com:\n";
try {
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'email' => 'rosa.karenderia@email.com',
        'password' => 'password123'
    ]);
    
    $response = $authController->login($request);
    $responseData = json_decode($response->getContent(), true);
    
    if (isset($responseData['access_token'])) {
        echo "✅ Login successful! Token: " . substr($responseData['access_token'], 0, 20) . "...\n";
        
        // Test the karenderia endpoint
        \Illuminate\Support\Facades\Auth::loginUsingId($responseData['user']['id']);
        
        $karenderiaController = new \App\Http\Controllers\KarenderiaController();
        $request = new \Illuminate\Http\Request();
        $request->setUserResolver(function() use ($responseData) {
            return \App\Models\User::find($responseData['user']['id']);
        });
        
        $karenderiaResponse = $karenderiaController->myKarenderia($request);
        $karenderiaData = json_decode($karenderiaResponse->getContent(), true);
        
        echo "API Response: " . json_encode($karenderiaData, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "❌ Login failed: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";