<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Karenderia Operating Days Debug ===\n";

$karenderia = App\Models\Karenderia::first();

if ($karenderia) {
    echo "Karenderia found: " . $karenderia->name . "\n";
    echo "Operating days raw value: ";
    var_dump($karenderia->operating_days);
    echo "Type: " . gettype($karenderia->operating_days) . "\n";
    echo "Opening time: " . $karenderia->opening_time . "\n";
    echo "Closing time: " . $karenderia->closing_time . "\n";
    
    // Test JSON decode if it's a string
    if (is_string($karenderia->operating_days)) {
        echo "JSON decoded: ";
        var_dump(json_decode($karenderia->operating_days));
    }
} else {
    echo "No karenderia found\n";
}