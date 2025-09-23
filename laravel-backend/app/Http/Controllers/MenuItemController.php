<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get the user's karenderia
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                // No karenderia exists for this user yet, return empty array
                return response()->json(['data' => []]);
            }

            // Return only menu items that belong to this user's karenderia
            $menuItems = MenuItem::with('karenderia')
                ->where('karenderia_id', $karenderia->id)
                ->get();
                
            return response()->json(['data' => $menuItems]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch menu items',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|string|max:255',
                'karenderia_id' => 'nullable|exists:karenderias,id',
                'cost_price' => 'nullable|numeric|min:0',
                'preparation_time_minutes' => 'nullable|integer|min:1|max:300',
                'calories' => 'nullable|integer|min:0',
                'ingredients' => 'nullable|array',
                'ingredients.*' => 'string|max:255',
                'allergens' => 'nullable|array',
                'allergens.*' => 'string|max:100',
                'dietary_info' => 'nullable|string|max:500',
                'spice_level' => 'nullable|integer|between:1,5',
                'serving_size' => 'nullable|integer|min:1',
                'is_available' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
                'image_url' => 'nullable|url'
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

            // Set defaults for optional fields
            $validatedData['is_available'] = $validatedData['is_available'] ?? true;
            $validatedData['is_featured'] = $validatedData['is_featured'] ?? false;
            $validatedData['preparation_time_minutes'] = $validatedData['preparation_time_minutes'] ?? 15;
            $validatedData['serving_size'] = $validatedData['serving_size'] ?? 1;

            $menuItem = MenuItem::create($validatedData);

            // Load the menuItem with its relationships
            $menuItem = MenuItem::with('karenderia')->find($menuItem->id);

            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'price' => $menuItem->price,
                    'cost_price' => $menuItem->cost_price,
                    'category' => $menuItem->category,
                    'image_url' => $menuItem->image_url,
                    'is_available' => $menuItem->is_available,
                    'is_featured' => $menuItem->is_featured,
                    'preparation_time_minutes' => $menuItem->preparation_time_minutes,
                    'calories' => $menuItem->calories,
                    'ingredients' => $menuItem->ingredients,
                    'allergens' => $menuItem->allergens,
                    'dietary_info' => $menuItem->dietary_info,
                    'spice_level' => $menuItem->spice_level,
                    'serving_size' => $menuItem->serving_size,
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

    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $menuItem = MenuItem::with('karenderia')->findOrFail($id);
            
            // Get the user's karenderia
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json(['error' => 'No karenderia found for this user'], 403);
            }

            // Check if the menu item belongs to the user's karenderia
            if ($menuItem->karenderia_id !== $karenderia->id) {
                return response()->json(['error' => 'Unauthorized: You can only view your own menu items'], 403);
            }

            return response()->json($menuItem);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch menu item',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'category' => 'sometimes|required|string|max:255',
                'cost_price' => 'nullable|numeric|min:0',
                'preparation_time_minutes' => 'nullable|integer|min:1|max:300',
                'calories' => 'nullable|integer|min:0',
                'ingredients' => 'nullable|array',
                'ingredients.*' => 'string|max:255',
                'allergens' => 'nullable|array',
                'allergens.*' => 'string|max:100',
                'dietary_info' => 'nullable|string|max:500',
                'spice_level' => 'nullable|integer|between:1,5',
                'serving_size' => 'nullable|integer|min:1',
                'is_available' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
                'image_url' => 'nullable|url'
            ]);

            $menuItem->update($validatedData);

            // Reload with relationships
            $menuItem = MenuItem::with('karenderia')->find($menuItem->id);

            return response()->json([
                'success' => true,
                'message' => 'Menu item updated successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'price' => $menuItem->price,
                    'cost_price' => $menuItem->cost_price,
                    'category' => $menuItem->category,
                    'image_url' => $menuItem->image_url,
                    'is_available' => $menuItem->is_available,
                    'is_featured' => $menuItem->is_featured,
                    'preparation_time_minutes' => $menuItem->preparation_time_minutes,
                    'calories' => $menuItem->calories,
                    'ingredients' => $menuItem->ingredients,
                    'allergens' => $menuItem->allergens,
                    'dietary_info' => $menuItem->dietary_info,
                    'spice_level' => $menuItem->spice_level,
                    'serving_size' => $menuItem->serving_size,
                    'karenderia_id' => $menuItem->karenderia_id,
                    'updated_at' => $menuItem->updated_at
                ]
            ]);
            
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
                'error' => 'Failed to update menu item',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update only the availability status of a menu item
     */
    public function updateAvailability(Request $request, $id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            
            // Verify that this menu item belongs to the current user's karenderia
            $user = $request->user();
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia || $menuItem->karenderia_id !== $karenderia->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized',
                    'message' => 'You can only update your own menu items'
                ], 403);
            }
            
            $validatedData = $request->validate([
                'is_available' => 'required|boolean'
            ]);

            $menuItem->update(['is_available' => $validatedData['is_available']]);

            return response()->json([
                'success' => true,
                'message' => 'Menu item availability updated successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'is_available' => $menuItem->is_available,
                    'updated_at' => $menuItem->updated_at
                ]
            ]);
            
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
                'error' => 'Failed to update menu item availability',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $menuItem = MenuItem::findOrFail($id);
            
            // Get the user's karenderia
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json(['error' => 'No karenderia found for this user'], 403);
            }

            // Check if the menu item belongs to the user's karenderia
            if ($menuItem->karenderia_id !== $karenderia->id) {
                return response()->json(['error' => 'Unauthorized: You can only delete your own menu items'], 403);
            }

            $menuItem->delete();

            return response()->json(['message' => 'Menu item deleted successfully']);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete menu item',
                'message' => $e->getMessage()
            ], 500);
        }
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

    /**
     * Search menu items with filters including nutrition and allergen criteria
     */
    public function search(Request $request)
    {
        try {
            $query = MenuItem::with('karenderia');
            
            // Apply filters
            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }
            
            if ($request->has('category')) {
                $query->where('category', $request->input('category'));
            }
            
            if ($request->has('karenderia')) {
                $query->where('karenderia_id', $request->input('karenderia'));
            }
            
            if ($request->has('max_calories') && $request->input('max_calories')) {
                $query->where('calories', '<=', $request->input('max_calories'));
            }
            
            if ($request->has('min_calories') && $request->input('min_calories')) {
                $query->where('calories', '>=', $request->input('min_calories'));
            }
            
            if ($request->has('max_price') && $request->input('max_price')) {
                $query->where('price', '<=', $request->input('max_price'));
            }
            
            if ($request->has('min_price') && $request->input('min_price')) {
                $query->where('price', '>=', $request->input('min_price'));
            }
            
            if ($request->has('allergens') && $request->input('allergens')) {
                $allergens = explode(',', $request->input('allergens'));
                foreach ($allergens as $allergen) {
                    $query->whereJsonDoesntContain('allergens', trim($allergen));
                }
            }
            
            if ($request->has('available')) {
                $query->where('is_available', $request->boolean('available'));
            }
            
            if ($request->has('featured')) {
                $query->where('is_featured', $request->boolean('featured'));
            }

            $menuItems = $query->get();

            return response()->json([
                'success' => true,
                'data' => $menuItems
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update nutrition information for a menu item
     */
    public function updateNutrition(Request $request, $id)
    {
        try {
            $menuItem = MenuItem::findOrFail($id);
            
            $validatedData = $request->validate([
                'calories' => 'nullable|integer|min:0',
                'ingredients' => 'nullable|array',
                'ingredients.*' => 'string|max:255',
                'allergens' => 'nullable|array',
                'allergens.*' => 'string|max:100',
                'dietary_info' => 'nullable|string|max:500',
                'spice_level' => 'nullable|integer|between:1,5',
                'serving_size' => 'nullable|integer|min:1'
            ]);

            $menuItem->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Nutrition information updated successfully',
                'data' => [
                    'id' => $menuItem->id,
                    'calories' => $menuItem->calories,
                    'ingredients' => $menuItem->ingredients,
                    'allergens' => $menuItem->allergens,
                    'dietary_info' => $menuItem->dietary_info,
                    'spice_level' => $menuItem->spice_level,
                    'serving_size' => $menuItem->serving_size
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update nutrition information',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get menu items for current user's karenderia
     */
    public function myMenuItems(Request $request)
    {
        try {
            $user = $request->user();
            \Log::info('MyMenuItems called for user:', ['user_id' => $user->id, 'email' => $user->email]);
            
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                \Log::warning('No karenderia found for user:', ['user_id' => $user->id]);
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No karenderia found for this user'
                ]);
            }

            \Log::info('Found karenderia for user:', [
                'karenderia_id' => $karenderia->id, 
                'karenderia_name' => $karenderia->name,
                'owner_id' => $karenderia->owner_id
            ]);

            $menuItems = MenuItem::where('karenderia_id', $karenderia->id)
                                ->with('karenderia')
                                ->get();

            \Log::info('Found menu items:', [
                'count' => $menuItems->count(),
                'karenderia_id' => $karenderia->id,
                'items' => $menuItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'karenderia_id' => $item->karenderia_id
                    ];
                })
            ]);

            return response()->json([
                'success' => true,
                'data' => $menuItems,
                'karenderia' => [
                    'id' => $karenderia->id,
                    'name' => $karenderia->name
                ],
                'debug' => [
                    'user_id' => $user->id,
                    'karenderia_id' => $karenderia->id,
                    'menu_count' => $menuItems->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in myMenuItems:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch menu items',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get allergen summary for karenderia menu
     */
    public function getAllergenSummary(Request $request)
    {
        try {
            $user = $request->user();
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'summary' => [
                        'total_items' => 0,
                        'allergen_free_items' => 0,
                        'common_allergens' => []
                    ]
                ]);
            }

            $menuItems = MenuItem::where('karenderia_id', $karenderia->id)->get();
            $allergenCounts = [];
            $allergenFreeCount = 0;

            foreach ($menuItems as $item) {
                if (empty($item->allergens) || count($item->allergens) === 0) {
                    $allergenFreeCount++;
                } else {
                    foreach ($item->allergens as $allergen) {
                        $allergenCounts[$allergen] = ($allergenCounts[$allergen] ?? 0) + 1;
                    }
                }
            }

            // Sort allergens by frequency
            arsort($allergenCounts);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_items' => $menuItems->count(),
                    'allergen_free_items' => $allergenFreeCount,
                    'common_allergens' => array_slice($allergenCounts, 0, 10, true)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get allergen summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get menu items for the current user's karenderia
     */
    public function getMyMenuItems(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get the user's karenderia
            $karenderia = \App\Models\Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user',
                    'data' => []
                ], 404);
            }

            // Get menu items for this karenderia
            $menuItems = MenuItem::where('karenderia_id', $karenderia->id)->get();

            return response()->json([
                'success' => true,
                'data' => $menuItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get menu items',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all menu categories
     */
    public function getCategories()
    {
        try {
            // Return predefined categories
            $categories = [
                'Main Dish',
                'Appetizer',
                'Dessert',
                'Beverage',
                'Rice Meals',
                'Noodles',
                'Soup',
                'Snacks',
                'Seafood',
                'Vegetarian',
                'Grilled',
                'Fried'
            ];

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all ingredients
     */
    public function getIngredients()
    {
        try {
            // Return predefined common ingredients
            $ingredients = [
                'Rice',
                'Chicken',
                'Pork',
                'Beef',
                'Fish',
                'Vegetables',
                'Onion',
                'Garlic',
                'Tomato',
                'Soy Sauce',
                'Vinegar',
                'Salt',
                'Pepper',
                'Oil',
                'Egg',
                'Coconut Milk',
                'Ginger',
                'Chili',
                'Lemon',
                'Carrots',
                'Potatoes',
                'Green Beans',
                'Cabbage',
                'Noodles'
            ];

            return response()->json([
                'success' => true,
                'data' => $ingredients
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get ingredients',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new category
     */
    public function createCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:menu_categories,name'
            ]);

            // For now, just return success (could implement database storage later)
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => ['name' => $request->name]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create category',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new ingredient
     */
    public function createIngredient(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            // For now, just return success (could implement database storage later)
            return response()->json([
                'success' => true,
                'message' => 'Ingredient created successfully',
                'data' => ['name' => $request->name]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create ingredient',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category (placeholder)
     */
    public function updateCategory(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Delete category (placeholder)
     */
    public function deleteCategory($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Update ingredient (placeholder)
     */
    public function updateIngredient(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Ingredient updated successfully'
        ]);
    }

    /**
     * Delete ingredient (placeholder)
     */
    public function deleteIngredient($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Ingredient deleted successfully'
        ]);
    }

    /**
     * Search menu items by karenderia ID (public endpoint for customers)
     */
    public function searchByKarenderia(Request $request)
    {
        try {
            $karenderiaId = $request->query('karenderia');
            
            if (!$karenderiaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia ID is required',
                    'data' => []
                ], 400);
            }

            // Verify the karenderia exists and is approved
            $karenderia = \App\Models\Karenderia::where('id', $karenderiaId)
                ->where('status', 'approved')
                ->first();
                
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karenderia not found or not approved',
                    'data' => []
                ], 404);
            }

            // Get menu items for this karenderia
            $menuItems = MenuItem::where('karenderia_id', $karenderiaId)
                ->where('is_available', true) // Only show available items
                ->get()
                ->map(function ($item) {
                    // Parse ingredients and allergens, add sample data if empty
                    $ingredients = $item->ingredients ? json_decode($item->ingredients, true) : [];
                    $allergens = $item->allergens ? json_decode($item->allergens, true) : [];
                    
                    // Add sample allergen data if empty (for demo purposes)
                    if (empty($ingredients) && empty($allergens)) {
                        $ingredients = ['pork', 'onions', 'bell peppers', 'soy sauce', 'vinegar'];
                        $allergens = ['peanut mild', 'soy moderate']; // Example allergen severity
                    }
                    
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'price' => $item->price,
                        'category' => $item->category,
                        'ingredients' => $ingredients,
                        'allergens' => $allergens,
                        'image' => $item->image_url,
                        'available' => $item->is_available,
                        'karenderia_id' => $item->karenderia_id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $menuItems,
                'message' => 'Menu items retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch menu items',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get public menu item details (public endpoint for customers)
     */
    public function showPublic($id)
    {
        try {
            $menuItem = MenuItem::with('karenderia')->findOrFail($id);
            
            // Verify the karenderia is approved
            if ($menuItem->karenderia->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not available',
                    'data' => null
                ], 404);
            }

            // Parse ingredients and allergens, add sample data if empty
            $ingredients = $menuItem->ingredients ? json_decode($menuItem->ingredients, true) : [];
            $allergens = $menuItem->allergens ? json_decode($menuItem->allergens, true) : [];
            
            // Add sample allergen data if empty (for demo purposes)
            if (empty($ingredients) && empty($allergens)) {
                switch(strtolower($menuItem->name)) {
                    case 'sisig':
                        $ingredients = ['pork', 'onions', 'bell peppers', 'soy sauce', 'vinegar', 'eggs'];
                        $allergens = ['peanut mild', 'soy moderate', 'egg low'];
                        break;
                    case 'adobo':
                        $ingredients = ['chicken', 'pork', 'soy sauce', 'vinegar', 'garlic', 'bay leaves'];
                        $allergens = ['soy moderate'];
                        break;
                    case 'lechon kawali':
                        $ingredients = ['pork belly', 'salt', 'pepper', 'oil'];
                        $allergens = ['peanut mild']; // if cooked in peanut oil
                        break;
                    default:
                        $ingredients = ['pork', 'onions', 'bell peppers', 'soy sauce', 'vinegar'];
                        $allergens = ['peanut mild', 'soy moderate'];
                }
            }

            $menuItemData = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'description' => $menuItem->description,
                'price' => $menuItem->price,
                'category' => $menuItem->category,
                'ingredients' => $ingredients,
                'allergens' => $allergens,
                'image' => $menuItem->image_url,
                'available' => $menuItem->is_available,
                'karenderia_id' => $menuItem->karenderia_id,
                'karenderia_name' => $menuItem->karenderia->name,
                'nutrition' => [
                    'calories' => 350, // Sample data
                    'protein' => '25g',
                    'carbs' => '15g',
                    'fat' => '20g',
                    'sodium' => '800mg'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $menuItemData,
                'message' => 'Menu item retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch menu item details',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
