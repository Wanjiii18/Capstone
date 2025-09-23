<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Karenderia;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get inventory for karenderia owner
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get the user's karenderia
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json(['error' => 'No karenderia found for this user'], 403);
            }

            $inventory = $this->inventoryService->getInventoryForKarenderia($karenderia->id);
            $stats = $this->inventoryService->getInventoryStats($karenderia->id);

            return response()->json([
                'data' => $inventory,
                'stats' => $stats,
                'karenderia' => $karenderia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch inventory',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock items
     */
    public function lowStock(Request $request)
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json(['error' => 'No karenderia found'], 403);
            }

            $lowStockItems = $this->inventoryService->getLowStockItems($karenderia->id);
            $outOfStockItems = $this->inventoryService->getOutOfStockItems($karenderia->id);

            return response()->json([
                'low_stock' => $lowStockItems,
                'out_of_stock' => $outOfStockItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch stock alerts',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new inventory item
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json(['error' => 'No karenderia found'], 403);
            }

            $validatedData = $request->validate([
                'item_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string|max:255',
                'unit' => 'required|string|max:255',
                'current_stock' => 'required|numeric|min:0',
                'minimum_stock' => 'required|numeric|min:0',
                'maximum_stock' => 'nullable|numeric|min:0',
                'unit_cost' => 'required|numeric|min:0',
                'supplier' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date',
                'notes' => 'nullable|string'
            ]);

            $validatedData['karenderia_id'] = $karenderia->id;
            
            $inventory = $this->inventoryService->createInventoryItem($validatedData);

            return response()->json([
                'message' => 'Inventory item created successfully',
                'data' => $inventory
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create inventory item',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inventory item
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $inventory = Inventory::findOrFail($id);
            
            // Check if user owns this inventory item
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            if (!$karenderia || $inventory->karenderia_id !== $karenderia->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validatedData = $request->validate([
                'item_name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'category' => 'sometimes|string|max:255',
                'unit' => 'sometimes|string|max:255',
                'current_stock' => 'sometimes|numeric|min:0',
                'minimum_stock' => 'sometimes|numeric|min:0',
                'maximum_stock' => 'nullable|numeric|min:0',
                'unit_cost' => 'sometimes|numeric|min:0',
                'supplier' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date',
                'notes' => 'nullable|string'
            ]);

            $success = $this->inventoryService->updateInventoryItem($id, $validatedData);

            if (!$success) {
                return response()->json(['error' => 'Failed to update inventory item'], 500);
            }

            $inventory->refresh();

            return response()->json([
                'message' => 'Inventory item updated successfully',
                'data' => $inventory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update inventory item',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restock inventory item
     */
    public function restock(Request $request, $id)
    {
        try {
            $user = $request->user();
            $inventory = Inventory::findOrFail($id);
            
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            if (!$karenderia || $inventory->karenderia_id !== $karenderia->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validatedData = $request->validate([
                'quantity' => 'required|numeric|min:0.001',
                'unit_cost' => 'nullable|numeric|min:0'
            ]);

            $success = $this->inventoryService->addStock(
                $id, 
                $validatedData['quantity'],
                $validatedData['unit_cost'] ?? null
            );

            if (!$success) {
                return response()->json(['error' => 'Failed to restock item'], 500);
            }

            $inventory->refresh();

            return response()->json([
                'message' => 'Item restocked successfully',
                'data' => $inventory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to restock item',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete inventory item
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $inventory = Inventory::findOrFail($id);
            
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            if (!$karenderia || $inventory->karenderia_id !== $karenderia->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $success = $this->inventoryService->deleteInventoryItem($id);

            if (!$success) {
                return response()->json([
                    'error' => 'Cannot delete inventory item',
                    'message' => 'This item is linked to daily menu items'
                ], 400);
            }

            return response()->json([
                'message' => 'Inventory item deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete inventory item',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
