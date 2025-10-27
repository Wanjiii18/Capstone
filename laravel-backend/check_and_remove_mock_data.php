<?php

/**
 * Check and Remove Mock/Test Data Script
 * This script identifies and removes mock data from your database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking for Mock/Test Data ===\n\n";

// Get all karenderias
$karenderias = DB::table('karenderias')->get();

echo "Total Karenderias in database: " . $karenderias->count() . "\n\n";

// Identify mock data patterns
$mockPatterns = [
    'URGENT TEST',
    'urgent.test@',
    'Juan\'s Carenderia',
    'Lisa\'s Fusion Kitchen',
    'Maria\'s Home Kitchen',
    'test',
    'demo',
    'sample',
    '@test.com',
    '@example.com',
];

$mockData = [];
$realData = [];

foreach ($karenderias as $karenderia) {
    $isMock = false;
    
    // Check if any field contains mock patterns
    $fields = (array) $karenderia;
    foreach ($fields as $field => $value) {
        if (is_string($value)) {
            foreach ($mockPatterns as $pattern) {
                if (stripos($value, $pattern) !== false) {
                    $isMock = true;
                    break 2;
                }
            }
        }
    }
    
    if ($isMock) {
        $mockData[] = $karenderia;
    } else {
        $realData[] = $karenderia;
    }
}

echo "=== Analysis ===\n";
echo "Mock/Test Data Found: " . count($mockData) . "\n";
echo "Real Data Found: " . count($realData) . "\n\n";

if (count($mockData) > 0) {
    echo "=== Mock Data Identified ===\n";
    foreach ($mockData as $mock) {
        echo "ID: {$mock->id}\n";
        echo "  Business: " . ($mock->business_name ?? 'N/A') . "\n";
        echo "  Email: " . ($mock->email ?? 'N/A') . "\n";
        echo "  Status: " . ($mock->status ?? 'N/A') . "\n";
        echo "  ---\n";
    }
    
    echo "\n=== Action Required ===\n";
    echo "Found " . count($mockData) . " mock/test entries.\n";
    echo "To delete them, run: php remove_mock_data.php\n\n";
} else {
    echo "✓ No mock data found! All data appears to be real.\n\n";
}

if (count($realData) > 0) {
    echo "=== Real Data (will be kept) ===\n";
    foreach ($realData as $real) {
        echo "ID: {$real->id}\n";
        echo "  Business: " . ($real->business_name ?? 'N/A') . "\n";
        echo "  Email: " . ($real->email ?? 'N/A') . "\n";
        echo "  Status: " . ($real->status ?? 'N/A') . "\n";
        echo "  ---\n";
    }
}

echo "\n=== Summary ===\n";
echo "This script only identifies mock data.\n";
echo "To actually delete mock data, create and run the removal script.\n";
