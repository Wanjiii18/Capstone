<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking karenderias table structure...\n\n";

try {
    $columns = DB::select('DESCRIBE karenderias');
    echo "Current karenderias table columns:\n";
    foreach ($columns as $column) {
        echo "  {$column->Field} ({$column->Type})" . ($column->Null === 'YES' ? ' [NULL]' : ' [NOT NULL]') . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Checking what registerKarenderiaOwner tries to insert...\n\n";
    
    // Check the Karenderia model fillable fields
    $karenderia = new \App\Models\Karenderia();
    $fillable = $karenderia->getFillable();
    
    echo "Karenderia model fillable fields:\n";
    foreach ($fillable as $field) {
        echo "  {$field}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>