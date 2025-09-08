<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class CleanDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate menu items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Finding and removing duplicate menu items...');
        
        // Get all menu items grouped by name and karenderia_id
        $duplicateGroups = MenuItem::select('name', 'karenderia_id', DB::raw('COUNT(*) as count'))
            ->groupBy('name', 'karenderia_id')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicateGroups->isEmpty()) {
            $this->info('✅ No duplicates found!');
            return;
        }
        
        $this->warn("🚨 Found {$duplicateGroups->count()} groups with duplicates:");
        
        $totalDeleted = 0;
        
        foreach ($duplicateGroups as $group) {
            $this->line("🔍 Processing: '{$group->name}' in karenderia {$group->karenderia_id} ({$group->count} copies)");
            
            // Get all items in this group, ordered by created_at (keep oldest)
            $items = MenuItem::where('name', $group->name)
                ->where('karenderia_id', $group->karenderia_id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Keep the first one, delete the rest
            $keep = $items->first();
            $toDelete = $items->skip(1);
            
            $this->line("   ✅ Keeping: ID {$keep->id} (created: {$keep->created_at})");
            
            foreach ($toDelete as $item) {
                $this->line("   ❌ Deleting: ID {$item->id} (created: {$item->created_at})");
                $item->delete();
                $totalDeleted++;
            }
        }
        
        $this->info("🎉 Cleanup completed!");
        $this->info("📈 Total duplicates removed: {$totalDeleted}");
        
        // Show final statistics
        $remainingCount = MenuItem::count();
        $this->info("📊 Total menu items remaining: {$remainingCount}");
        
        // Show items by karenderia
        $this->line("\n📋 Menu items by karenderia:");
        $byKarenderia = MenuItem::with('karenderia')
            ->get()
            ->groupBy('karenderia_id');
        
        foreach ($byKarenderia as $karenderiaId => $items) {
            $karenderia = $items->first()->karenderia;
            $karenderiaName = $karenderia ? $karenderia->business_name : "Unknown Karenderia {$karenderiaId}";
            $this->line("   📍 {$karenderiaName}: " . count($items) . " items");
            
            foreach ($items as $item) {
                $this->line("      - {$item->name}");
            }
        }
        
        return 0;
    }
}
