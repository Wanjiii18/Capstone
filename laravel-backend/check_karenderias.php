<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Existing Karenderias ===\n";

try {
    // Check if karenderias table exists and get data
    $karenderias = \App\Models\Karenderia::all();
    
    echo "Found " . $karenderias->count() . " karenderias in database:\n";
    
    foreach($karenderias as $karenderia) {
        echo "\n" . str_repeat('-', 50) . "\n";
        echo "ID: {$karenderia->id}\n";
        echo "Name: {$karenderia->name}\n";
        echo "Address: {$karenderia->address}\n";
        echo "Latitude: " . ($karenderia->latitude ?? 'NOT SET') . "\n";
        echo "Longitude: " . ($karenderia->longitude ?? 'NOT SET') . "\n";
        echo "Status: {$karenderia->status}\n";
        echo "Owner ID: {$karenderia->owner_id}\n";
    }
    
    echo "\n" . str_repeat('=', 50) . "\n";
    echo "Summary:\n";
    echo "- Total Karenderias: " . $karenderias->count() . "\n";
    echo "- With Coordinates: " . $karenderias->whereNotNull('latitude')->whereNotNull('longitude')->count() . "\n";
    echo "- Without Coordinates: " . $karenderias->where('latitude', null)->orWhere('longitude', null)->count() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Make sure the karenderias table exists and Laravel server is properly configured.\n";
}
