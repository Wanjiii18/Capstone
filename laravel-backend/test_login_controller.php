<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING LOGIN CONTROLLER DIRECTLY ===\n\n";

// Create a mock request
$request = new Illuminate\Http\Request();
$request->merge([
    'email' => 'alica@gmail.com',
    'password' => 'password123'
]);

echo "Testing login for: alica@gmail.com\n";
echo "Password: password123\n\n";

try {
    // Test the AuthController login method directly
    $controller = new App\Http\Controllers\AuthController();
    $response = $controller->login($request);
    
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    $content = $response->getContent();
    echo $content . "\n\n";
    
    $data = json_decode($content, true);
    
    if ($response->getStatusCode() === 200) {
        echo "✅ Login successful!\n";
        if (isset($data['access_token'])) {
            echo "✅ Token received\n";
        }
        if (isset($data['user'])) {
            echo "User role: " . $data['user']['role'] . "\n";
        }
        if (isset($data['karenderia'])) {
            echo "Karenderia: " . $data['karenderia']['business_name'] . "\n";
            echo "Status: " . $data['karenderia']['status'] . "\n";
        }
    } else {
        echo "❌ Login failed\n";
        if (isset($data['message'])) {
            echo "Error: " . $data['message'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== CHECKING USER DETAILS ===\n";
$user = App\Models\User::where('email', 'alica@gmail.com')->with('karenderia')->first();
if ($user) {
    echo "User ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Role: {$user->role}\n";
    echo "Verified: " . ($user->verified ? 'Yes' : 'No') . "\n";
    
    if ($user->karenderia) {
        echo "Karenderia ID: {$user->karenderia->id}\n";
        echo "Business: {$user->karenderia->business_name}\n";
        echo "Status: {$user->karenderia->status}\n";
        echo "Approved: " . ($user->karenderia->approved_at ?? 'No') . "\n";
    }
}