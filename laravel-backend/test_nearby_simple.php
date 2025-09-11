<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing nearby karenderias...\n";

// Test coordinates (Talisay City)
$lat = 10.2442;
$lng = 123.8492;
$radius = 10; // 10km

echo "Looking for karenderias near Talisay City ($lat, $lng) within {$radius}km\n\n";

// Direct database query using Haversine formula
$karenderias = DB::select("
    SELECT 
        id,
        name,
        latitude,
        longitude,
        address,
        status,
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
    FROM karenderias 
    WHERE status = 'active'
    HAVING distance < ?
    ORDER BY distance
", [$lat, $lng, $lat, $radius]);

echo "Found " . count($karenderias) . " karenderias:\n";

foreach ($karenderias as $karenderia) {
    echo "- {$karenderia->name} ({$karenderia->distance}km away) at {$karenderia->address}\n";
}

echo "\nDone!\n";
