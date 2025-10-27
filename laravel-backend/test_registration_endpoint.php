<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Http\Request;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test data for karenderia owner registration
$testData = [
    'name' => 'Test Owner ' . time(),
    'email' => 'testowner' . time() . '@test.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'business_name' => 'Test Karenderia ' . time(),
    'description' => 'A test karenderia for debugging',
    'address' => '123 Test Street',
    'city' => 'Test City',
    'province' => 'Test Province',
    'phone' => '09123456789',
    'business_email' => 'business' . time() . '@test.com',
    'opening_time' => '08:00',
    'closing_time' => '18:00'
];

echo "========================================\n";
echo "Testing Karenderia Owner Registration\n";
echo "========================================\n\n";

echo "Test Data:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

try {
    // Create a request
    $request = Request::create('/api/auth/register-karenderia-owner', 'POST', $testData);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    // Handle the request
    $response = $kernel->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Body:\n";
    echo $response->getContent() . "\n\n";
    
    // Check if user was created
    $pdo = new PDO('mysql:host=localhost;dbname=kaplato_db', 'root', 'root');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$testData['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ User Created:\n";
        echo "   ID: {$user['id']}\n";
        echo "   Name: {$user['name']}\n";
        echo "   Email: {$user['email']}\n";
        echo "   Role: {$user['role']}\n\n";
        
        // Check if karenderia was created
        $stmt = $pdo->prepare("SELECT * FROM karenderias WHERE owner_id = ?");
        $stmt->execute([$user['id']]);
        $karenderia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($karenderia) {
            echo "✅ Karenderia Created:\n";
            echo "   ID: {$karenderia['id']}\n";
            echo "   Business Name: {$karenderia['business_name']}\n";
            echo "   Status: {$karenderia['status']}\n";
            echo "   Owner ID: {$karenderia['owner_id']}\n\n";
        } else {
            echo "❌ No karenderia found for this user!\n\n";
        }
    } else {
        echo "❌ User was NOT created!\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
