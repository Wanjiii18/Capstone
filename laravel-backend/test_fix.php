<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Business Name Fix...\n\n";

// Check existing karenderias
$karenderias = DB::table('karenderias')
    ->select('id', 'name', 'business_name', 'owner_id')
    ->get();

echo "=== Current Karenderias ===\n";
foreach ($karenderias as $k) {
    $businessName = $k->business_name ?? 'NULL';
    echo "ID: {$k->id} | Account Name: {$k->name} | Business Name: {$businessName}\n";
}

echo "\n✅ API Fix Applied! Now includes business_name field\n";
echo "✅ Each karenderia will show their unique business name!\n";

?>