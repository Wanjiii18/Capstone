<?php
// Test the user profile API endpoint with joseph2's token
$url = 'http://localhost:8000/api/user/profile';

echo "=== TESTING USER PROFILE API ENDPOINT ===\n\n";

// We need to get joseph2's token. Let me create a fresh one for testing.
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Find joseph2 user
    $user = \App\Models\User::where('email', 'joseph2@gmail.com')->first();
    if (!$user) {
        echo "❌ User not found!\n";
        exit;
    }
    
    // Create a new token for testing
    $token = $user->createToken('test_token')->plainTextToken;
    echo "✅ Created test token for joseph2@gmail.com\n";
    echo "Token: {$token}\n\n";
    
    // Now test the API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    echo "HTTP Code: $httpCode\n";
    echo "Raw Response:\n";
    echo $response . "\n\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "=== PARSED USER PROFILE DATA ===\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "=== KEY PROFILE FIELDS ===\n";
        echo "Display Name: " . ($data['displayName'] ?? 'N/A') . "\n";
        echo "Email: " . ($data['email'] ?? 'N/A') . "\n";
        echo "Phone: " . ($data['phoneNumber'] ?? 'N/A') . "\n";
        echo "Address: " . ($data['address'] ?? 'N/A') . "\n";
        echo "Role: " . ($data['role'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Profile API failed!\n";
        echo "Response: $response\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>