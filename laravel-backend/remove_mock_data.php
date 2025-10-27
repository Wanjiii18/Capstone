<?php

/**
 * Remove Mock/Test Data Script
 * This script safely deletes mock data from your database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Removing Mock/Test Data ===\n\n";

// Mock data IDs to remove (based on check_and_remove_mock_data.php results)
$mockIds = [
    5,  // Tabada Kitchen (rejected)
    19, // URGENT TEST KITCHEN (pending)
    20, // Maria's Home Kitchen (pending)
    22, // Lisa's Fusion Kitchen (pending)
    25, // sample Kitchen (rejected)
    26, // Test Karenderia Business (approved)
    27, // Flow Test Karenderia (approved)
];

echo "Mock data IDs to be deleted: " . implode(', ', $mockIds) . "\n\n";

// Start transaction for safety
DB::beginTransaction();

try {
    // Get owner IDs associated with these karenderias first
    $ownerIds = DB::table('karenderias')
        ->whereIn('id', $mockIds)
        ->pluck('owner_id')
        ->filter()
        ->unique()
        ->toArray();
    
    echo "Associated owner IDs: " . (count($ownerIds) > 0 ? implode(', ', $ownerIds) : 'None') . "\n\n";
    
    // Delete related data first (to avoid foreign key constraints)
    
    // 1. Delete menu items
    $deletedMenuItems = DB::table('menu_items')
        ->whereIn('karenderia_id', $mockIds)
        ->delete();
    echo "✓ Deleted {$deletedMenuItems} menu items\n";
    
    // 2. Delete daily menus
    $deletedDailyMenus = DB::table('daily_menus')
        ->whereIn('karenderia_id', $mockIds)
        ->delete();
    echo "✓ Deleted {$deletedDailyMenus} daily menus\n";
    
    // 3. Delete inventory items
    $deletedInventory = DB::table('inventory')
        ->whereIn('karenderia_id', $mockIds)
        ->delete();
    echo "✓ Deleted {$deletedInventory} inventory items\n";
    
    // 4. Delete karenderias
    $deletedKarenderias = DB::table('karenderias')
        ->whereIn('id', $mockIds)
        ->delete();
    echo "✓ Deleted {$deletedKarenderias} karenderias\n";
    
    // 5. Optionally delete associated users (only if they have no other data)
    if (count($ownerIds) > 0) {
        echo "\n=== Associated Users ===\n";
        foreach ($ownerIds as $ownerId) {
            $user = DB::table('users')->where('id', $ownerId)->first();
            if ($user) {
                // Check if user has any other karenderias
                $otherKarenderias = DB::table('karenderias')
                    ->where('owner_id', $ownerId)
                    ->count();
                
                if ($otherKarenderias == 0) {
                    DB::table('users')->where('id', $ownerId)->delete();
                    echo "  ✓ Deleted user {$ownerId} ({$user->email})\n";
                } else {
                    echo "  ⊗ Kept user {$ownerId} ({$user->email}) - has {$otherKarenderias} other karenderias\n";
                }
            }
        }
    }
    
    // Commit transaction
    DB::commit();
    
    echo "\n=== Success! ===\n";
    echo "✅ All mock/test data has been removed from your database.\n";
    echo "✅ Your real data has been preserved.\n\n";
    
    // Show remaining data
    $remainingCount = DB::table('karenderias')->count();
    echo "Remaining karenderias in database: {$remainingCount}\n\n";
    
    $remaining = DB::table('karenderias')
        ->select('id', 'business_name', 'status')
        ->get();
    
    echo "=== Your Real Data ===\n";
    foreach ($remaining as $karenderia) {
        echo "ID: {$karenderia->id} - {$karenderia->business_name} ({$karenderia->status})\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back. No data was deleted.\n";
}
