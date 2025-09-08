<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Configure database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_DATABASE', 'kaplato_db'),
    'username' => env('DB_USERNAME', 'postgres'),
    'password' => env('DB_PASSWORD', 'password'),
    'charset' => 'utf8',
    'prefix' => '',
    'schema' => 'public',
    'sslmode' => 'prefer',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Cleaning up duplicate menu items...\n";

try {
    // Find duplicates by name and karenderia_id
    $duplicates = DB::table('menu_items')
        ->select('name', 'karenderia_id', DB::raw('COUNT(*) as count'))
        ->groupBy('name', 'karenderia_id')
        ->having('count', '>', 1)
        ->get();

    echo "Found " . count($duplicates) . " sets of duplicates:\n";

    foreach ($duplicates as $duplicate) {
        echo "\n🔍 Processing duplicates: '{$duplicate->name}' in karenderia {$duplicate->karenderia_id} ({$duplicate->count} copies)\n";
        
        // Get all items with this name and karenderia_id, ordered by created_at
        $items = DB::table('menu_items')
            ->where('name', $duplicate->name)
            ->where('karenderia_id', $duplicate->karenderia_id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Keep the first one (oldest), delete the rest
        $keep = $items->first();
        $toDelete = $items->skip(1);
        
        echo "   ✅ Keeping: ID {$keep->id} (created: {$keep->created_at})\n";
        
        foreach ($toDelete as $item) {
            echo "   ❌ Deleting: ID {$item->id} (created: {$item->created_at})\n";
            DB::table('menu_items')->where('id', $item->id)->delete();
        }
    }

    echo "\n✅ Cleanup completed!\n";
    
    // Show final count
    $finalCount = DB::table('menu_items')->count();
    echo "Total menu items remaining: {$finalCount}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
