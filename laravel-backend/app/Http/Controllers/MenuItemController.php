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

    /**
     * Search menu items with filters
     */
    public function search(Request $request)
    {
        $query = MenuItem::with(['karenderia', 'reviews']);
        
        // Search by name/description
        if ($request->has('query') && !empty($request->query)) {
            $searchTerm = $request->query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        // Filter by price range
        if ($request->has('priceMin') && is_numeric($request->priceMin)) {
            $query->where('price', '>=', $request->priceMin);
        }
        if ($request->has('priceMax') && is_numeric($request->priceMax)) {
            $query->where('price', '<=', $request->priceMax);
        }
        
        // Filter by calories
        if ($request->has('calories') && is_numeric($request->calories)) {
            $query->where('calories', '<=', $request->calories);
        }
        
        // Filter by allergens (exclude items with these allergens)
        if ($request->has('allergens') && !empty($request->allergens)) {
            $allergens = explode(',', $request->allergens);
            foreach ($allergens as $allergen) {
                $query->whereJsonDoesntContain('allergens', trim($allergen));
            }
        }
        
        // Filter by dietary tags
        if ($request->has('dietary') && !empty($request->dietary)) {
            $dietary = explode(',', $request->dietary);
            foreach ($dietary as $tag) {
                $query->whereJsonContains('dietary_tags', trim($tag));
            }
        }
        
        // Filter by karenderia
        if ($request->has('karenderia') && !empty($request->karenderia)) {
            $query->where('karenderia_id', $request->karenderia);
        }
        
        // Only show available items
        $query->where('is_available', true);
        
        $results = $query->get()->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image' => $item->image,
                'karenderia_id' => $item->karenderia_id,
                'karenderia_name' => $item->karenderia->name ?? 'Unknown',
                'calories' => $item->calories,
                'allergens' => $item->allergens,
                'dietary_tags' => $item->dietary_tags,
                'average_rating' => $item->reviews ? $item->reviews->avg('rating') : null,
                'total_reviews' => $item->reviews ? $item->reviews->count() : 0,
                'preparation_time' => $item->preparation_time,
                'available' => $item->is_available
            ];
        });
        
        return response()->json(['data' => $results]);
    }

    /**
     * Get detailed menu item information
     */
    public function getDetails($id)
    {
        $menuItem = MenuItem::with(['karenderia', 'reviews.user'])->find($id);
        
        if (!$menuItem) {
            return response()->json(['error' => 'Menu item not found'], 404);
        }
        
        $details = [
            'id' => $menuItem->id,
            'name' => $menuItem->name,
            'price' => $menuItem->price,
            'description' => $menuItem->description,
            'image' => $menuItem->image,
            'karenderia_id' => $menuItem->karenderia_id,
            'karenderia_name' => $menuItem->karenderia->name ?? 'Unknown',
            'karenderia_address' => $menuItem->karenderia->address ?? null,
            'calories' => $menuItem->calories,
            'protein' => $menuItem->protein,
            'carbs' => $menuItem->carbs,
            'fat' => $menuItem->fat,
            'allergens' => $menuItem->allergens,
            'ingredients' => $menuItem->ingredients,
            'dietary_tags' => $menuItem->dietary_tags,
            'spiciness_level' => $menuItem->spiciness_level,
            'preparation_time' => $menuItem->preparation_time,
            'average_rating' => $menuItem->reviews ? $menuItem->reviews->avg('rating') : null,
            'total_reviews' => $menuItem->reviews ? $menuItem->reviews->count() : 0,
            'available' => $menuItem->is_available,
            'category' => $menuItem->category,
            'reviews' => $menuItem->reviews ? $menuItem->reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'user_avatar' => $review->user->avatar ?? null,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'helpful_count' => $review->helpful_count ?? 0
                ];
            })->sortByDesc('created_at')->values() : []
        ];
        
        return response()->json(['data' => $details]);
    }

    /**
     * Add a review to a menu item
     */
    public function addReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);
        
        $menuItem = MenuItem::find($id);
        if (!$menuItem) {
            return response()->json(['error' => 'Menu item not found'], 404);
        }
        
        // Check if user already reviewed this item
        $existingReview = \App\Models\Review::where([
            'user_id' => $request->user()->id,
            'menu_item_id' => $id
        ])->first();
        
        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->review ?? ''
            ]);
            $review = $existingReview;
        } else {
            // Create new review
            $review = \App\Models\Review::create([
                'user_id' => $request->user()->id,
                'menu_item_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->review ?? ''
            ]);
        }
        
        return response()->json([
            'message' => 'Review added successfully',
            'data' => $review
        ]);
    }

    /**
     * Get reviews for a menu item
     */
    public function getReviews($id)
    {
        $reviews = \App\Models\Review::with('user')
            ->where('menu_item_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name ?? 'Anonymous',
                    'user_avatar' => $review->user->avatar ?? null,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'helpful_count' => $review->helpful_count ?? 0
                ];
            });
        
        return response()->json(['data' => $reviews]);
    }
}
