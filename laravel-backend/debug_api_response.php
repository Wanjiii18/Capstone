<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Debug: Full API Response ===\n\n";

// Login first
$user = App\Models\User::where('email', 'owner@kaplato.com')->first();
if (!$user) {
    echo "❌ User not found\n";
    exit(1);
}

// Simulate authentication
auth()->login($user);

$controller = new App\Http\Controllers\KarenderiaController();
$response = $controller->getMyKarenderia();
$content = $response->getContent();

echo "🔍 Raw API Response:\n";
echo $content . "\n\n";

$data = json_decode($content, true);
echo "📊 Parsed Data:\n";
print_r($data);

// Check what's actually in the database
echo "\n📁 Database Check:\n";
$karenderia = App\Models\Karenderia::where('owner_id', $user->id)->first();
if ($karenderia) {
    echo "Found karenderia in DB:\n";
    echo "  ID: {$karenderia->id}\n";
    echo "  Name: {$karenderia->name}\n";
    echo "  Business Name: " . ($karenderia->business_name ?? 'NULL') . "\n";
    echo "  All fields: " . json_encode($karenderia->toArray(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ No karenderia found in database for this user\n";
}
