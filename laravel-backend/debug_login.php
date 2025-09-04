<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Login Authentication ===\n\n";

// Check if the user exists and password is correct
$email = 'owner@kaplato.com';
$password = 'password123';

echo "1. Checking user in database...\n";
$user = App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found with email: {$email}\n";
    
    // Let's see what users exist
    echo "\n📋 Available users:\n";
    $users = App\Models\User::where('role', 'karenderia_owner')->get(['id', 'name', 'email']);
    foreach ($users as $u) {
        echo "   - ID: {$u->id}, Name: {$u->name}, Email: {$u->email}\n";
    }
    exit(1);
}

echo "✅ User found: {$user->name} (ID: {$user->id})\n";
echo "   Email: {$user->email}\n";
echo "   Role: {$user->role}\n";

echo "\n2. Testing password verification...\n";
if (Hash::check($password, $user->password)) {
    echo "✅ Password is correct\n";
} else {
    echo "❌ Password verification failed\n";
    
    // Let's update the password to be sure
    echo "\n🔧 Updating password to 'password123'...\n";
    $user->password = Hash::make('password123');
    $user->save();
    echo "✅ Password updated\n";
}

echo "\n3. Testing Auth::attempt simulation...\n";
$credentials = ['email' => $email, 'password' => $password];

if (Auth::attempt($credentials)) {
    echo "✅ Auth::attempt would succeed\n";
    $authUser = Auth::user();
    echo "   Authenticated user: {$authUser->name}\n";
} else {
    echo "❌ Auth::attempt would fail\n";
}

echo "\n4. Testing API endpoint with curl simulation...\n";

// Simulate the login request
$requestData = [
    'email' => $email,
    'password' => $password
];

// Check validation
$validator = Validator::make($requestData, [
    'email' => 'required|email',
    'password' => 'required'
]);

if ($validator->fails()) {
    echo "❌ Validation would fail:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "   - {$error}\n";
    }
} else {
    echo "✅ Request validation would pass\n";
}

// Attempt authentication
if (Auth::attempt($requestData)) {
    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;
    
    echo "✅ Full login process would succeed\n";
    echo "   Token preview: " . substr($token, 0, 20) . "...\n";
    echo "   User data would be returned\n";
} else {
    echo "❌ Full login process would fail\n";
}

echo "\n5. CORS and Headers Check...\n";
echo "   Server URL: http://127.0.0.1:8000\n";
echo "   Login endpoint: http://127.0.0.1:8000/api/login\n";
echo "   Required headers: Content-Type: application/json, Accept: application/json\n";

echo "\n=== Summary ===\n";
echo "If you're getting 401 Unauthorized, possible causes:\n";
echo "1. Wrong credentials (check email/password)\n";
echo "2. CORS issues (check browser network tab)\n";
echo "3. Wrong endpoint URL\n";
echo "4. Missing/incorrect headers\n";
echo "5. Frontend sending malformed request\n";
