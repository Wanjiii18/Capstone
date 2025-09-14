<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Real Login and API Response ===\n\n";

// Get a karenderia owner that exists
$user = DB::table('users')
    ->where('role', 'karenderia_owner')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('karenderias')
              ->whereRaw('karenderias.owner_id = users.id');
    })
    ->first();

if (!$user) {
    echo "❌ No karenderia owners found\n";
    exit;
}

echo "Testing with karenderia owner: {$user->name} ({$user->email})\n";

// Get their karenderia
$karenderia = DB::table('karenderias')->where('owner_id', $user->id)->first();
echo "Their karenderia:\n";
echo "  Name: {$karenderia->name}\n";
echo "  Business Name: {$karenderia->business_name}\n";
echo "  Status: {$karenderia->status}\n\n";

// Test login process
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

$authController = new AuthController();

// Simulate login request
$loginData = [
    'email' => $user->email,
    'password' => 'password123' // Assuming default password
];

$loginRequest = new Request($loginData);

try {
    echo "Testing login...\n";
    $loginResponse = $authController->login($loginRequest);
    $loginStatusCode = $loginResponse->getStatusCode();
    $loginResponseData = json_decode($loginResponse->getContent(), true);
    
    echo "Login Status: $loginStatusCode\n";
    
    if ($loginStatusCode === 200) {
        echo "✅ Login successful!\n";
        echo "User info from login:\n";
        echo "  ID: {$loginResponseData['user']['id']}\n";
        echo "  Name: {$loginResponseData['user']['name']}\n";
        echo "  Role: {$loginResponseData['user']['role']}\n";
        echo "  Token: " . substr($loginResponseData['access_token'], 0, 20) . "...\n\n";
        
        // Now test myKarenderia with proper authentication
        $token = $loginResponseData['access_token'];
        
        // Create an authenticated request
        $karenderiaRequest = new Request();
        $karenderiaRequest->headers->set('Authorization', 'Bearer ' . $token);
        
        // Manually authenticate the user for testing
        $authUser = \App\Models\User::find($loginResponseData['user']['id']);
        auth()->login($authUser);
        
        $karenderiaController = new \App\Http\Controllers\KarenderiaController();
        
        echo "Testing myKarenderia endpoint...\n";
        $karenderiaResponse = $karenderiaController->myKarenderia($karenderiaRequest);
        $karenderiaStatusCode = $karenderiaResponse->getStatusCode();
        $karenderiaResponseData = json_decode($karenderiaResponse->getContent(), true);
        
        echo "MyKarenderia Status: $karenderiaStatusCode\n";
        echo "MyKarenderia Response:\n";
        echo json_encode($karenderiaResponseData, JSON_PRETTY_PRINT) . "\n\n";
        
        if ($karenderiaStatusCode === 200 && isset($karenderiaResponseData['data'])) {
            $data = $karenderiaResponseData['data'];
            echo "🔍 Frontend Display Analysis:\n";
            echo "  API returns name: '" . ($data['name'] ?? 'MISSING') . "'\n";
            echo "  API returns business_name: '" . ($data['business_name'] ?? 'MISSING') . "'\n";
            echo "  Frontend template uses: {{ karenderia.name }}\n";
            echo "  Frontend displays: '" . ($data['name'] ?? 'MISSING') . "'\n\n";
            
            if ($data['name'] === "Mama's Kitchen") {
                echo "🎯 FOUND THE ISSUE!\n";
                echo "The database has 'Mama's Kitchen' as the name field\n";
                echo "Need to either:\n";
                echo "1. Update frontend to use business_name instead of name\n";
                echo "2. Fix the database data\n";
            } else {
                echo "✅ Database seems correct, issue might be elsewhere\n";
            }
        }
        
    } else {
        echo "❌ Login failed\n";
        echo "Response: " . json_encode($loginResponseData, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>