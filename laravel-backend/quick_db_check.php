<?php
// Simple test to check what's in the database for your user

// Check if we're in the right directory
if (!file_exists('vendor/autoload.php')) {
    echo "❌ Error: Not in Laravel directory. Please run this from laravel-backend folder.\n";
    exit;
}

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Karenderia;

echo "🔍 DIRECT DATABASE CHECK\n";
echo "========================\n\n";

// Test your users
$testEmails = ['test@gmail.com', 'last@gmail.com'];

foreach ($testEmails as $email) {
    echo "👤 Checking user: {$email}\n";
    
    $user = User::where('email', $email)->first();
    
    if (!$user) {
        echo "   ❌ User not found\n\n";
        continue;
    }
    
    echo "   ✅ User found: {$user->name} (ID: {$user->id})\n";
    
    $karenderia = Karenderia::where('owner_id', $user->id)->first();
    
    if (!$karenderia) {
        echo "   📝 No karenderia registered for this user\n";
    } else {
        echo "   🏪 Karenderia found:\n";
        echo "      Name: {$karenderia->name}\n";
        echo "      Business: {$karenderia->business_name}\n";
        echo "      Status: {$karenderia->status}\n";
        echo "      Description: {$karenderia->description}\n";
        echo "      Address: {$karenderia->address}\n";
        echo "      Phone: {$karenderia->phone}\n";
        echo "      Email: {$karenderia->email}\n";
        
        // This is what the API should return
        $apiResponse = [
            'success' => true,
            'data' => [
                'id' => $karenderia->id,
                'name' => $karenderia->name,
                'business_name' => $karenderia->business_name,
                'description' => $karenderia->description,
                'address' => $karenderia->address,
                'phone' => $karenderia->phone,
                'email' => $karenderia->email,
                'status' => $karenderia->status,
                'latitude' => $karenderia->latitude,
                'longitude' => $karenderia->longitude,
            ]
        ];
        
        echo "\n   📡 Expected API Response:\n";
        echo json_encode($apiResponse, JSON_PRETTY_PRINT);
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "💡 IMPORTANT: If you see the correct names here but frontend shows 'Mama's Kitchen',\n";
echo "   then the frontend is NOT calling the backend API properly!\n\n";