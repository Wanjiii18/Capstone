<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Karenderia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Get all menu items for a specific karenderia
     */
    public function index(Request $request): JsonResponse
    {
        $karenderiaId = $request->input('karenderia_id');
        
        $query = MenuItem::with('karenderia');
        
        if ($karenderiaId) {
            $query->where('karenderia_id', $karenderiaId);
        }
        
        $menuItems = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $menuItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => $item->price,
                    'category' => $item->category,
                    'image_url' => $item->image_url,
                    'ingredients' => $item->ingredients ?? [],
                    'allergens' => $item->allergens ?? [],
                    'is_available' => $item->is_available,
                    'preparation_time' => $item->preparation_time,
                    'calories' => $item->calories,
                    'spicy_level' => $item->spicy_level,
                    'karenderia_id' => $item->karenderia_id,
                    'karenderia_name' => $item->karenderia->name ?? null
                ];
            })
        ]);
    }

    /**
     * Create a new menu item
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Menu item creation request:', [
                'data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            $validatedData = $request->validate([
                'karenderia_id' => 'sometimes|nullable|integer',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
                'image_url' => 'nullable|string',
                'ingredients' => 'nullable|array',
                'allergens' => 'nullable|array',
                'is_available' => 'nullable|boolean',
                'preparation_time' => 'nullable|integer|min:1',
                'calories' => 'nullable|integer|min:0',
                'spicy_level' => 'nullable|integer|min:0|max:5'
            ]);

            // Set default karenderia_id if not provided
            if (!isset($validatedData['karenderia_id'])) {
                $validatedData['karenderia_id'] = 1;
            }

            $menuItem = MenuItem::create($validatedData);
            
            \Log::info('Menu item created successfully:', ['id' => $menuItem->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'price' => $menuItem->price,
                    'category' => $menuItem->category,
                    'image_url' => $menuItem->image_url,
                    'ingredients' => $menuItem->ingredients ?? [],
                    'allergens' => $menuItem->allergens ?? [],
                    'is_available' => $menuItem->is_available,
                    'preparation_time' => $menuItem->preparation_time,
                    'calories' => $menuItem->calories,
                    'spicy_level' => $menuItem->spicy_level,
                    'karenderia_id' => $menuItem->karenderia_id
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Menu item creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific menu item
     */
    public function show($id): JsonResponse
    {
        $menuItem = MenuItem::with('karenderia')->find($id);
        
        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'description' => $menuItem->description,
                'price' => $menuItem->price,
                'category' => $menuItem->category,
                'image_url' => $menuItem->image_url,
                'ingredients' => $menuItem->ingredients ?? [],
                'allergens' => $menuItem->allergens ?? [],
                'is_available' => $menuItem->is_available,
                'preparation_time' => $menuItem->preparation_time,
                'calories' => $menuItem->calories,
                'spicy_level' => $menuItem->spicy_level,
                'karenderia_id' => $menuItem->karenderia_id,
                'karenderia_name' => $menuItem->karenderia->name ?? null
            ]
        ]);
    }

    /**
     * Update a menu item
     */
    public function update(Request $request, $id): JsonResponse
    {
        $menuItem = MenuItem::find($id);
        
        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }
        
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:100',
            'image_url' => 'sometimes|nullable|string|url',
            'ingredients' => 'sometimes|nullable|array',
            'allergens' => 'sometimes|nullable|array',
            'is_available' => 'sometimes|boolean',
            'preparation_time' => 'sometimes|nullable|integer|min:1',
            'calories' => 'sometimes|nullable|integer|min:0',
            'spicy_level' => 'sometimes|integer|min:0|max:5'
        ]);

        $menuItem->update($validatedData);
        
        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'description' => $menuItem->description,
                'price' => $menuItem->price,
                'category' => $menuItem->category,
                'image_url' => $menuItem->image_url,
                'ingredients' => $menuItem->ingredients ?? [],
                'allergens' => $menuItem->allergens ?? [],
                'is_available' => $menuItem->is_available,
                'preparation_time' => $menuItem->preparation_time,
                'calories' => $menuItem->calories,
                'spicy_level' => $menuItem->spicy_level,
                'karenderia_id' => $menuItem->karenderia_id
            ]
        ]);
    }

    /**
     * Delete a menu item
     */
    public function destroy($id): JsonResponse
    {
        $menuItem = MenuItem::find($id);
        
        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }
        
        $menuItem->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully'
        ]);
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability($id): JsonResponse
    {
        $menuItem = MenuItem::find($id);
        
        if (!$menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found'
            ], 404);
        }
        
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        
        return response()->json([
            'success' => true,
            'message' => 'Menu item availability updated successfully',
            'data' => [
                'id' => $menuItem->id,
                'is_available' => $menuItem->is_available
            ]
        ]);
    }
}
