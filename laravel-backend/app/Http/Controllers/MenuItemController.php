<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuItemController extends Controller
{
    public function index()
    {
        // For now, return all menu items to debug the issue
        $menuItems = MenuItem::with('karenderia')->get();
        return response()->json(['data' => $menuItems]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'description' => 'nullable|string',
                'category' => 'nullable|string|max:255',
                'karenderia_id' => 'nullable|exists:karenderias,id',
            ]);

            // Set default category if not provided
            if (!isset($validatedData['category'])) {
                $validatedData['category'] = 'Main Dish';
            }

            // If karenderia_id is not provided, create or get a default karenderia for this user
            if (!isset($validatedData['karenderia_id'])) {
                $user = $request->user();
                
                if (!$user) {
                    return response()->json(['error' => 'User not authenticated'], 401);
                }
                
                // Try to find existing karenderia for this user
                $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
                
                // If no karenderia exists, create a default one
                if (!$karenderia) {
                    $karenderia = \App\Models\Karenderia::create([
                        'name' => $user->name . "'s Karenderia",
                        'description' => 'Default karenderia for ' . $user->name,
                        'address' => $user->address ?? 'Default Address',
                        'owner_id' => $user->id,
                        'status' => 'active'
                    ]);
                }
                
                $validatedData['karenderia_id'] = $karenderia->id;
            }

            $menuItem = MenuItem::create($validatedData);

            // Load the menuItem with its relationships
            $menuItem = MenuItem::with('karenderia')->find($menuItem->id);

            return response()->json([
                'success' => true,
                'message' => 'Menu item added successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'price' => $menuItem->price,
                    'category' => $menuItem->category,
                    'image_url' => $menuItem->image_url,
                    'is_available' => $menuItem->is_available,
                    'karenderia_id' => $menuItem->karenderia_id,
                    'created_at' => $menuItem->created_at,
                    'updated_at' => $menuItem->updated_at
                ]
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid input data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create menu item',
                'message' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        return response()->json($menuItem);
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update($request->all());

        return response()->json(['message' => 'Menu item updated successfully', 'menuItem' => $menuItem]);
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete();

        return response()->json(['message' => 'Menu item deleted successfully']);
    }

    public function getDailySales(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $user = $request->user();
        
        // Get user's karenderia
        $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
        
        if (!$karenderia) {
            return response()->json(['sales' => 0, 'orders' => 0, 'date' => $date]);
        }

        // Mock data for now - replace with actual sales calculation
        return response()->json([
            'sales' => 1500.00,
            'orders' => 12,
            'date' => $date,
            'karenderia_id' => $karenderia->id
        ]);
    }

    public function getMonthlySales(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $user = $request->user();
        
        // Get user's karenderia
        $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
        
        if (!$karenderia) {
            return response()->json(['sales' => 0, 'orders' => 0, 'month' => $month]);
        }

        // Mock data for now - replace with actual sales calculation
        return response()->json([
            'sales' => 45000.00,
            'orders' => 380,
            'month' => $month,
            'karenderia_id' => $karenderia->id
        ]);
    }

    public function getSalesSummary(Request $request)
    {
        $user = $request->user();
        
        // Get user's karenderia
        $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
        
        if (!$karenderia) {
            return response()->json([
                'total_sales' => 0,
                'total_orders' => 0,
                'total_menu_items' => 0,
                'average_order_value' => 0
            ]);
        }

        $menuItemsCount = MenuItem::where('karenderia_id', $karenderia->id)->count();

        // Mock data for now - replace with actual calculations
        return response()->json([
            'total_sales' => 125000.00,
            'total_orders' => 850,
            'total_menu_items' => $menuItemsCount,
            'average_order_value' => 147.06,
            'karenderia_id' => $karenderia->id
        ]);
    }
}
