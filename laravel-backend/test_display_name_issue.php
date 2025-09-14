<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing API Response for Logged-In Karenderia Owner ===\n\n";

// Get a karenderia owner that exists
$karenderia = DB::table('karenderias')
    ->join('users', 'karenderias.owner_id', '=', 'users.id')
    ->select('karenderias.*', 'users.name as owner_name', 'users.email as owner_email')
    ->orderBy('karenderias.id', 'desc')
    ->first();

if (!$karenderia) {
    echo "❌ No karenderias found in database\n";
    exit;
}

echo "Found karenderia in database:\n";
echo "  ID: {$karenderia->id}\n";
echo "  Name: {$karenderia->name}\n";
echo "  Business Name: {$karenderia->business_name}\n";
echo "  Owner: {$karenderia->owner_name} ({$karenderia->owner_email})\n\n";

// Test what the API returns
use App\Http\Controllers\KarenderiaController;
use Illuminate\Http\Request;
use App\Models\User;

$user = User::find($karenderia->owner_id);
if (!$user) {
    echo "❌ User not found\n";
    exit;
}

// Simulate authenticated request
auth()->login($user);

$controller = new KarenderiaController();
$request = new Request();

try {
    echo "Testing KarenderiaController::myKarenderia endpoint...\n";
    $response = $controller->myKarenderia($request);
    $statusCode = $response->getStatusCode();
    $responseData = json_decode($response->getContent(), true);
    
    echo "Status Code: $statusCode\n";
    echo "API Response:\n";
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($statusCode === 200 && isset($responseData['data'])) {
        $data = $responseData['data'];
        echo "Frontend will receive:\n";
        echo "  karenderia.name = " . ($data['name'] ?? 'MISSING') . "\n";
        echo "  karenderia.business_name = " . ($data['business_name'] ?? 'MISSING') . "\n\n";
        
        if (isset($data['business_name']) && $data['business_name'] !== $data['name']) {
            echo "✅ API includes business_name field\n";
            echo "🔍 Issue: Frontend using karenderia.name instead of karenderia.business_name\n";
        } else {
            echo "❌ API business_name field issue\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Check what the default/hardcoded name might be
$possibleDefaults = [
    "Mama's Kitchen",
    "mama's kitchen", 
    "Default Karenderia",
    "Test Kitchen"
];

foreach ($possibleDefaults as $default) {
    $count = DB::table('karenderias')->where('name', $default)->count();
    if ($count > 0) {
        echo "Found $count karenderia(s) with name '$default'\n";
    }
}

?>