<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing KarenderiaController index() method ===\n";

try {
    // Create controller instance
    $controller = new App\Http\Controllers\KarenderiaController();
    
    // Create a mock request
    $request = new Illuminate\Http\Request();
    
    // Call the index method
    echo "Calling controller index method...\n";
    $response = $controller->index($request);
    
    echo "Response status: " . $response->status() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}