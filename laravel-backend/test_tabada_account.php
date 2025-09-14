<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing tabada@gmail.com Account ===\n\n";

// Test login with tabada account
$email = 'tabada@gmail.com';
$password = 'password123'; // Default password

// Check if user exists
$user = DB::table('users')->where('email', $email)->first();
if (!$user) {
    echo "❌ User {$email} not found\n";
    exit;
}

echo "User found:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Role: {$user->role}\n\n";

// Check their karenderia
$karenderia = DB::table('karenderias')->where('owner_id', $user->id)->first();
if (!$karenderia) {
    echo "❌ No karenderia found for this user\n";
    exit;
}

echo "Karenderia in database:\n";
echo "  ID: {$karenderia->id}\n";
echo "  Name: '{$karenderia->name}'\n";
echo "  Business Name: '{$karenderia->business_name}'\n";
echo "  Status: {$karenderia->status}\n\n";

// Test the actual API endpoint that frontend calls
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$authController = new AuthController();
$loginRequest = new Request([
    'email' => $email,
    'password' => $password
]);

try {
    echo "Testing login API...\n";
    $loginResponse = $authController->login($loginRequest);
    $loginData = json_decode($loginResponse->getContent(), true);
    
    if ($loginResponse->getStatusCode() === 200) {
        echo "✅ Login successful!\n";
        $token = $loginData['access_token'];
        
        // Test the my-karenderia endpoint with the token
        $user = \App\Models\User::find($loginData['user']['id']);
        auth()->login($user);
        
        $karenderiaController = new \App\Http\Controllers\KarenderiaController();
        $karenderiaRequest = new Request();
        
        echo "\nTesting /karenderias/my-karenderia endpoint...\n";
        $karenderiaResponse = $karenderiaController->myKarenderia($karenderiaRequest);
        $karenderiaData = json_decode($karenderiaResponse->getContent(), true);
        
        echo "API Response Status: {$karenderiaResponse->getStatusCode()}\n";
        echo "API Response:\n";
        echo json_encode($karenderiaData, JSON_PRETTY_PRINT) . "\n\n";
        
        if (isset($karenderiaData['data']['name'])) {
            $apiName = $karenderiaData['data']['name'];
            $apiBusinessName = $karenderiaData['data']['business_name'] ?? 'NOT_SET';
            
            echo "🔍 ANALYSIS:\n";
            echo "  Database name: '{$karenderia->name}'\n";
            echo "  Database business_name: '{$karenderia->business_name}'\n";
            echo "  API returns name: '{$apiName}'\n";
            echo "  API returns business_name: '{$apiBusinessName}'\n\n";
            
            if ($apiName === "Mama's Kitchen") {
                echo "🎯 FOUND THE ISSUE!\n";
                echo "The API is returning 'Mama's Kitchen' but database has '{$karenderia->name}'\n";
                echo "This suggests the API is not returning the correct data.\n";
            } else {
                echo "✅ API data matches database\n";
                echo "The 'Mama's Kitchen' issue might be frontend caching or different account.\n";
            }
        }
        
    } else {
        echo "❌ Login failed\n";
        echo "Response: " . json_encode($loginData, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>