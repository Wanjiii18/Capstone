<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "🔍 Testing MenuItemController search method directly...\n\n";

try {
    // Create a fake request
    $request = new Illuminate\Http\Request();
    $request->query->set('karenderia', '1');
    
    // Instantiate the controller
    $controller = new App\Http\Controllers\MenuItemController();
    
    // Call the search method
    $response = $controller->search($request);
    
    // Get the response content
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "✅ Response Status: " . $response->getStatusCode() . "\n";
    echo "📋 Response Content:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Trace:\n" . $e->getTraceAsString() . "\n";
}
?>
