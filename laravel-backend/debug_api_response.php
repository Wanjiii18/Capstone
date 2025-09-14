<?php
require_once 'bootstrap/app.php';

use Illuminate\Http\Request;
use App\Http\Controllers\KarenderiaController;
use App\Models\User;

echo "=== DEBUGGING API RESPONSE FOR TABADA USER ===\n\n";

// Find tabada user
$user = User::where('email', 'tabada@gmail.com')->first();
if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

echo "✅ User found: {$user->name} ({$user->email})\n";
echo "Role: {$user->role}\n\n";

// Set the authenticated user (simulate API auth)
auth()->login($user);

// Create controller instance
$controller = new KarenderiaController();

// Create a mock request
$request = new Request();

try {
    // Call the myKarenderia method
    $response = $controller->myKarenderia($request);
    
    // Get the response data
    $responseData = $response->getData(true);
    
    echo "=== COMPLETE API RESPONSE ===\n";
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($responseData['data'])) {
        echo "=== KARENDERIA DATA DETAILS ===\n";
        $karenderiaData = $responseData['data'];
        echo "ID: " . ($karenderiaData['id'] ?? 'N/A') . "\n";
        echo "Name: " . ($karenderiaData['name'] ?? 'N/A') . "\n";
        echo "Business Name: " . ($karenderiaData['business_name'] ?? 'N/A') . "\n";
        echo "Owner ID: " . ($karenderiaData['owner_id'] ?? 'N/A') . "\n";
        echo "Address: " . ($karenderiaData['address'] ?? 'N/A') . "\n";
        echo "Phone: " . ($karenderiaData['phone'] ?? 'N/A') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error calling API: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}