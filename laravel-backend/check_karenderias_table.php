<?php

require_once 'vendor/autoload.php';

// Set up Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Karenderias Table Structure ===\n\n";

try {
    $columns = \DB::select('DESCRIBE karenderias');
    
    echo "Karenderias table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n=== Sample Karenderia Records ===\n";
    $karenderias = \DB::table('karenderias')->limit(5)->get();
    
    if ($karenderias->isEmpty()) {
        echo "No karenderia records found.\n";
    } else {
        foreach ($karenderias as $k) {
            echo "ID: {$k->id}\n";
            echo "Name: {$k->name}\n";
            echo "Email: " . ($k->email ?? 'null') . "\n";
            echo "Phone: " . ($k->phoneNumber ?? 'null') . "\n";
            echo "Owner: " . ($k->owner ?? 'null') . "\n";
            echo "---\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";