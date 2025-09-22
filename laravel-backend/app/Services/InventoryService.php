<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\DailyMenu;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    /**
     * Get inventory items for a specific karenderia
     */
    public function getInventoryForKarenderia(int $karenderiaId): Collection
    {
        return Inventory::where('karenderia_id', $karenderiaId)
            ->orderBy('category')
            ->orderBy('item_name')
            ->get();
    }

    /**
     * Get low stock items for a karenderia
     */
    public function getLowStockItems(int $karenderiaId): Collection
    {
        return Inventory::where('karenderia_id', $karenderiaId)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();
    }

    /**
     * Get out of stock items for a karenderia
     */
    public function getOutOfStockItems(int $karenderiaId): Collection
    {
        return Inventory::where('karenderia_id', $karenderiaId)
            ->where('current_stock', '<=', 0)
            ->get();
    }

    /**
     * Check if there's enough stock for a daily menu item
     */
    public function checkStockAvailability(int $inventoryId, float $requiredQuantity): bool
    {
        $inventory = Inventory::find($inventoryId);
        
        if (!$inventory) {
            return false;
        }

        return $inventory->current_stock >= $requiredQuantity;
    }

    /**
     * Deduct stock when a daily menu item is ordered
     */
    public function deductStock(int $inventoryId, float $quantity, string $reason = 'Daily menu order'): bool
    {
        $inventory = Inventory::find($inventoryId);
        
        if (!$inventory || $inventory->current_stock < $quantity) {
            return false;
        }

        $inventory->current_stock -= $quantity;
        $inventory->total_value = $inventory->current_stock * $inventory->unit_cost;
        
        // Update status based on stock level
        if ($inventory->current_stock <= 0) {
            $inventory->status = 'out_of_stock';
        } elseif ($inventory->current_stock <= $inventory->minimum_stock) {
            $inventory->status = 'low_stock';
        } else {
            $inventory->status = 'available';
        }
        
        $inventory->save();
        
        return true;
    }

    /**
     * Add stock (for restocking)
     */
    public function addStock(int $inventoryId, float $quantity, float $unitCost = null): bool
    {
        $inventory = Inventory::find($inventoryId);
        
        if (!$inventory) {
            return false;
        }

        $inventory->current_stock += $quantity;
        
        if ($unitCost !== null) {
            $inventory->unit_cost = $unitCost;
        }
        
        $inventory->total_value = $inventory->current_stock * $inventory->unit_cost;
        $inventory->last_restocked = now()->toDateString();
        
        // Update status
        if ($inventory->current_stock > $inventory->minimum_stock) {
            $inventory->status = 'available';
        }
        
        $inventory->save();
        
        return true;
    }

    /**
     * Get available ingredients for daily menu creation
     */
    public function getAvailableIngredients(int $karenderiaId): Collection
    {
        return Inventory::where('karenderia_id', $karenderiaId)
            ->where('status', '!=', 'out_of_stock')
            ->where('current_stock', '>', 0)
            ->orderBy('category')
            ->orderBy('item_name')
            ->get();
    }

    /**
     * Calculate total inventory value for a karenderia
     */
    public function getTotalInventoryValue(int $karenderiaId): float
    {
        return Inventory::where('karenderia_id', $karenderiaId)
            ->sum('total_value');
    }

    /**
     * Get inventory statistics for dashboard
     */
    public function getInventoryStats(int $karenderiaId): array
    {
        $inventory = $this->getInventoryForKarenderia($karenderiaId);
        
        return [
            'total_items' => $inventory->count(),
            'total_value' => $inventory->sum('total_value'),
            'low_stock_count' => $inventory->where('current_stock', '<=', function($item) {
                return $item->minimum_stock;
            })->count(),
            'out_of_stock_count' => $inventory->where('current_stock', '<=', 0)->count(),
            'categories' => $inventory->pluck('category')->unique()->values(),
        ];
    }

    /**
     * Update inventory item
     */
    public function updateInventoryItem(int $inventoryId, array $data): bool
    {
        $inventory = Inventory::find($inventoryId);
        
        if (!$inventory) {
            return false;
        }

        // Update fields
        $inventory->fill($data);
        
        // Recalculate total value if stock or cost changed
        if (isset($data['current_stock']) || isset($data['unit_cost'])) {
            $inventory->total_value = $inventory->current_stock * $inventory->unit_cost;
        }
        
        // Auto-update status based on stock level
        if ($inventory->current_stock <= 0) {
            $inventory->status = 'out_of_stock';
        } elseif ($inventory->current_stock <= $inventory->minimum_stock) {
            $inventory->status = 'low_stock';
        } else {
            $inventory->status = 'available';
        }
        
        return $inventory->save();
    }

    /**
     * Create new inventory item
     */
    public function createInventoryItem(array $data): Inventory
    {
        $data['total_value'] = $data['current_stock'] * $data['unit_cost'];
        
        // Set initial status
        if ($data['current_stock'] <= 0) {
            $data['status'] = 'out_of_stock';
        } elseif ($data['current_stock'] <= $data['minimum_stock']) {
            $data['status'] = 'low_stock';
        } else {
            $data['status'] = 'available';
        }
        
        return Inventory::create($data);
    }

    /**
     * Delete inventory item (only if not linked to any daily menu)
     */
    public function deleteInventoryItem(int $inventoryId): bool
    {
        $inventory = Inventory::find($inventoryId);
        
        if (!$inventory) {
            return false;
        }

        // Check if this inventory item is linked to any daily menu
        $linkedDailyMenus = DailyMenu::where('inventory_id', $inventoryId)->count();
        
        if ($linkedDailyMenus > 0) {
            return false; // Cannot delete if linked to daily menus
        }

        return $inventory->delete();
    }
}