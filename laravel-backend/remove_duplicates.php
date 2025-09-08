<?php

echo "🔍 Finding and removing duplicate menu items...\n";

// Include Laravel bootstrap
require_once __DIR__ . '/bootstrap/app.php';

// Boot the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

try {
    echo "📊 Analyzing menu items for duplicates...\n";
    
    // Get all menu items grouped by name and karenderia_id
    $duplicateGroups = MenuItem::select('name', 'karenderia_id', DB::raw('COUNT(*) as count'))
        ->groupBy('name', 'karenderia_id')
        ->having('count', '>', 1)
        ->get();
    
    if ($duplicateGroups->isEmpty()) {
        echo "✅ No duplicates found!\n";
        exit;
    }
    
    echo "🚨 Found " . count($duplicateGroups) . " groups with duplicates:\n\n";
    
    $totalDeleted = 0;
    
    foreach ($duplicateGroups as $group) {
        echo "🔍 Processing: '{$group->name}' in karenderia {$group->karenderia_id} ({$group->count} copies)\n";
        
        // Get all items in this group, ordered by created_at (keep oldest)
        $items = MenuItem::where('name', $group->name)
            ->where('karenderia_id', $group->karenderia_id)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Keep the first one, delete the rest
        $keep = $items->first();
        $toDelete = $items->skip(1);
        
        echo "   ✅ Keeping: ID {$keep->id} (created: {$keep->created_at})\n";
        
        foreach ($toDelete as $item) {
            echo "   ❌ Deleting: ID {$item->id} (created: {$item->created_at})\n";
            $item->delete();
            $totalDeleted++;
        }
        
        echo "\n";
    }
    
    echo "🎉 Cleanup completed!\n";
    echo "📈 Total duplicates removed: {$totalDeleted}\n";
    
    // Show final statistics
    $remainingCount = MenuItem::count();
    echo "📊 Total menu items remaining: {$remainingCount}\n";
    
    // Show items by karenderia
    echo "\n📋 Menu items by karenderia:\n";
    $byKarenderia = MenuItem::with('karenderia')
        ->get()
        ->groupBy('karenderia_id');
    
    foreach ($byKarenderia as $karenderiaId => $items) {
        $karenderia = $items->first()->karenderia;
        $karenderiaName = $karenderia ? $karenderia->business_name : "Unknown Karenderia {$karenderiaId}";
        echo "   📍 {$karenderiaName}: " . count($items) . " items\n";
        
        foreach ($items as $item) {
            echo "      - {$item->name}\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
