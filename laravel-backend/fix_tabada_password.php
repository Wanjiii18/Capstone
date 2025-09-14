<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing tabada@gmail.com Password ===\n\n";

$email = 'tabada@gmail.com';
$newPassword = 'password123';

$user = DB::table('users')->where('email', $email)->first();

if ($user) {
    // Update password
    DB::table('users')
        ->where('email', $email)
        ->update([
            'password' => bcrypt($newPassword),
            'updated_at' => now()
        ]);
    
    echo "✅ Password updated for {$email}\n";
    echo "New password: {$newPassword}\n\n";
    
    // Test login now
    $authController = new \App\Http\Controllers\AuthController();
    $loginRequest = new \Illuminate\Http\Request([
        'email' => $email,
        'password' => $newPassword
    ]);
    
    try {
        $loginResponse = $authController->login($loginRequest);
        $loginData = json_decode($loginResponse->getContent(), true);
        
        if ($loginResponse->getStatusCode() === 200) {
            echo "✅ Login test successful!\n";
            echo "User: {$loginData['user']['name']}\n";
            echo "Token: " . substr($loginData['access_token'], 0, 20) . "...\n\n";
            
            // Now test the karenderia API
            $user = \App\Models\User::find($loginData['user']['id']);
            auth()->login($user);
            
            $karenderiaController = new \App\Http\Controllers\KarenderiaController();
            $karenderiaRequest = new \Illuminate\Http\Request();
            
            $karenderiaResponse = $karenderiaController->myKarenderia($karenderiaRequest);
            $karenderiaData = json_decode($karenderiaResponse->getContent(), true);
            
            echo "🔍 API Response for tabada account:\n";
            echo json_encode($karenderiaData, JSON_PRETTY_PRINT) . "\n\n";
            
            if (isset($karenderiaData['data']['name'])) {
                echo "✅ API returns: '{$karenderiaData['data']['name']}'\n";
                echo "✅ Business name: '{$karenderiaData['data']['business_name']}'\n";
                
                if ($karenderiaData['data']['name'] === 'tabada karenderia') {
                    echo "🎉 API is working correctly for tabada account!\n";
                } else {
                    echo "❓ Unexpected name returned\n";
                }
            }
            
        } else {
            echo "❌ Login still failing: " . json_encode($loginData, JSON_PRETTY_PRINT) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ User not found\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Try logging in with: tabada@gmail.com / password123\n";
echo "2. Clear your browser cache and localStorage\n";
echo "3. Check if you see 'tabada karenderia' instead of 'Mama's Kitchen'\n";

?>