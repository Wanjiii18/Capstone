<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing KarenderiaController ===\n";

try {
    // Test the actual controller logic
    $karenderias = App\Models\Karenderia::where('status', 'approved')->get();
    
    echo "Found " . $karenderias->count() . " approved karenderias\n";
    
    foreach ($karenderias as $karenderia) {
        echo "Testing karenderia: " . $karenderia->name . "\n";
        echo "Status: " . $karenderia->status . "\n";
        echo "Operating days: ";
        var_dump($karenderia->operating_days);
        echo "Opening time: " . $karenderia->opening_time . "\n";
        echo "Closing time: " . $karenderia->closing_time . "\n";
        
        // Test the isOpen logic
        echo "Testing time comparison...\n";
        if ($karenderia->opening_time && $karenderia->closing_time) {
            $openingTime = $karenderia->opening_time->format('H:i');
            $closingTime = $karenderia->closing_time->format('H:i');
            echo "Opening time formatted: $openingTime\n";
            echo "Closing time formatted: $closingTime\n";
        }
        
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}