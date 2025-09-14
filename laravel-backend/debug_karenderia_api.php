<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\KarenderiaController;
use App\Models\User;
use App\Models\Karenderia;

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debugging Karenderia API Response ===\n\n";

$testEmail = 'joseph2@gmail.com';

// Find the user
$user = User::where('email', $testEmail)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->displayName} (ID: {$user->id}, Role: {$user->role})\n\n";

// Check if user has a karenderia record in database
echo "1. Checking Karenderia Records:\n";
$karenderias = Karenderia::where('user_id', $user->id)->get();

if ($karenderias->isEmpty()) {
    echo "   ❌ No karenderia records found for user_id: {$user->id}\n";
} else {
    echo "   ✅ Found " . $karenderias->count() . " karenderia record(s):\n";
    foreach ($karenderias as $karenderia) {
        echo "   - ID: {$karenderia->id}\n";
        echo "   - Name: {$karenderia->name}\n";
        echo "   - Phone: {$karenderia->phoneNumber}\n";
        echo "   - Email: {$karenderia->email}\n";
        echo "   - Address: {$karenderia->address}\n";
        echo "   - Description: {$karenderia->description}\n";
        echo "   - Status: {$karenderia->status}\n";
        echo "   - Owner ID: {$karenderia->user_id}\n\n";
    }
}

// Test the controller method directly
echo "2. Testing KarenderiaController::myKarenderia():\n";

try {
    $karenderiaController = new KarenderiaController();
    
    // Create a mock request with the user
    $request = new Request();
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $response = $karenderiaController->myKarenderia($request);
    
    echo "   Response type: " . get_class($response) . "\n";
    
    // Get the response data
    if (method_exists($response, 'getData')) {
        $responseData = $response->getData(true);
        echo "   Response data: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   Response content: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";