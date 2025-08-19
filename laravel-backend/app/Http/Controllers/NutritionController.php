<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NutritionController extends Controller
{
    /**
     * Update nutrition information for a menu item
     */
    public function updateMenuItemNutrition(Request $request, $menuItemId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nutrition' => 'required|array',
                'nutrition.calories' => 'required|numeric|min:0',
                'nutrition.protein' => 'required|numeric|min:0',
                'nutrition.carbs' => 'required|numeric|min:0',
                'nutrition.fat' => 'required|numeric|min:0',
                'nutrition.fiber' => 'nullable|numeric|min:0',
                'nutrition.sodium' => 'nullable|numeric|min:0',
                'nutrition.sugar' => 'nullable|numeric|min:0',
                'allergens' => 'nullable|array',
                'spice_level' => 'nullable|string|in:mild,medium,spicy,very_spicy',
                'dietary_tags' => 'nullable|array',
                'serving_size' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $menuItem = MenuItem::findOrFail($menuItemId);

            // Update the menu item with nutrition data
            $menuItem->update([
                'calories' => $request->input('nutrition.calories'),
                'allergens' => $request->input('allergens', []),
                'spice_level' => $request->input('spice_level'),
                'dietary_info' => json_encode([
                    'nutrition' => $request->input('nutrition'),
                    'dietary_tags' => $request->input('dietary_tags', []),
                    'serving_size' => $request->input('serving_size'),
                    'updated_at' => now()
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nutrition information updated successfully',
                'data' => $menuItem
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update nutrition information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nutrition information for a menu item
     */
    public function getMenuItemNutrition($menuItemId): JsonResponse
    {
        try {
            $menuItem = MenuItem::findOrFail($menuItemId);

            $nutritionData = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'calories' => $menuItem->calories,
                'allergens' => $menuItem->allergens,
                'spice_level' => $menuItem->spice_level,
                'dietary_info' => $menuItem->dietary_info ? json_decode($menuItem->dietary_info, true) : null
            ];

            return response()->json([
                'success' => true,
                'data' => $nutritionData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get nutrition information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search menu items by nutrition criteria
     */
    public function searchByNutrition(Request $request): JsonResponse
    {
        try {
            $query = MenuItem::query();

            // Filter by calories
            if ($request->has('max_calories')) {
                $query->where('calories', '<=', $request->input('max_calories'));
            }

            if ($request->has('min_calories')) {
                $query->where('calories', '>=', $request->input('min_calories'));
            }

            // Filter by spice level
            if ($request->has('spice_level')) {
                $query->where('spice_level', $request->input('spice_level'));
            }

            // Filter by allergens (exclude items with specified allergens)
            if ($request->has('allergen_free')) {
                $allergenFree = explode(',', $request->input('allergen_free'));
                foreach ($allergenFree as $allergen) {
                    $query->whereJsonDoesntContain('allergens', trim($allergen));
                }
            }

            // Filter by dietary requirements
            if ($request->has('dietary_tags')) {
                $dietaryTags = explode(',', $request->input('dietary_tags'));
                foreach ($dietaryTags as $tag) {
                    $query->whereJsonContains('dietary_info->dietary_tags', trim($tag));
                }
            }

            // Filter by karenderia
            if ($request->has('karenderia_id')) {
                $query->where('karenderia_id', $request->input('karenderia_id'));
            }

            // Search by name
            if ($request->has('query')) {
                $searchQuery = $request->input('query');
                $query->where('name', 'like', "%{$searchQuery}%");
            }

            $menuItems = $query->where('is_available', true)
                              ->with('karenderia')
                              ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $menuItems->items(),
                'pagination' => [
                    'current_page' => $menuItems->currentPage(),
                    'total_pages' => $menuItems->lastPage(),
                    'total_items' => $menuItems->total(),
                    'per_page' => $menuItems->perPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search menu items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nutrition statistics for a karenderia's menu
     */
    public function getMenuNutritionStats($karenderiaId): JsonResponse
    {
        try {
            $menuItems = MenuItem::where('karenderia_id', $karenderiaId)
                                ->where('is_available', true)
                                ->get();

            $stats = [
                'total_items' => $menuItems->count(),
                'average_calories' => $menuItems->avg('calories'),
                'low_calorie_items' => $menuItems->where('calories', '<', 300)->count(),
                'high_calorie_items' => $menuItems->where('calories', '>', 600)->count(),
                'spice_distribution' => [
                    'mild' => $menuItems->where('spice_level', 'mild')->count(),
                    'medium' => $menuItems->where('spice_level', 'medium')->count(),
                    'spicy' => $menuItems->where('spice_level', 'spicy')->count(),
                    'very_spicy' => $menuItems->where('spice_level', 'very_spicy')->count(),
                ],
                'common_allergens' => $this->getCommonAllergens($menuItems),
                'dietary_options' => $this->getDietaryOptions($menuItems)
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get nutrition statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommended menu items based on user preferences
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            // Get user's allergen profile
            $userAllergens = $request->user()->allergens()->pluck('name')->toArray();
            
            $query = MenuItem::query()
                           ->where('is_available', true)
                           ->with('karenderia');

            // Exclude items with user's allergens
            foreach ($userAllergens as $allergen) {
                $query->whereJsonDoesntContain('allergens', $allergen);
            }

            // Apply preferences from request
            if ($request->has('max_calories')) {
                $query->where('calories', '<=', $request->input('max_calories'));
            }

            if ($request->has('preferred_spice_level')) {
                $query->where('spice_level', $request->input('preferred_spice_level'));
            }

            if ($request->has('dietary_preferences')) {
                $dietaryPrefs = explode(',', $request->input('dietary_preferences'));
                foreach ($dietaryPrefs as $pref) {
                    $query->whereJsonContains('dietary_info->dietary_tags', trim($pref));
                }
            }

            // Order by popularity and ratings
            $recommendations = $query->orderBy('total_orders', 'desc')
                                   ->orderBy('average_rating', 'desc')
                                   ->limit($request->input('limit', 10))
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'message' => 'Recommendations based on your preferences'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update nutrition data for multiple menu items
     */
    public function bulkUpdateNutrition(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array',
                'items.*.id' => 'required|exists:menu_items,id',
                'items.*.nutrition' => 'required|array',
                'items.*.allergens' => 'nullable|array',
                'items.*.spice_level' => 'nullable|string|in:mild,medium,spicy,very_spicy'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updatedItems = [];
            $errors = [];

            foreach ($request->input('items') as $itemData) {
                try {
                    $menuItem = MenuItem::find($itemData['id']);
                    if ($menuItem) {
                        $menuItem->update([
                            'calories' => $itemData['nutrition']['calories'] ?? null,
                            'allergens' => $itemData['allergens'] ?? [],
                            'spice_level' => $itemData['spice_level'] ?? null,
                            'dietary_info' => json_encode([
                                'nutrition' => $itemData['nutrition'],
                                'dietary_tags' => $itemData['dietary_tags'] ?? [],
                                'serving_size' => $itemData['serving_size'] ?? null,
                                'updated_at' => now()
                            ])
                        ]);
                        $updatedItems[] = $menuItem->id;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'item_id' => $itemData['id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk nutrition update completed',
                'data' => [
                    'updated_items' => $updatedItems,
                    'errors' => $errors,
                    'total_updated' => count($updatedItems),
                    'total_errors' => count($errors)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update nutrition data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get common allergens from menu items
     */
    private function getCommonAllergens($menuItems): array
    {
        $allergenCounts = [];
        
        foreach ($menuItems as $item) {
            if ($item->allergens) {
                foreach ($item->allergens as $allergen) {
                    $allergenCounts[$allergen] = ($allergenCounts[$allergen] ?? 0) + 1;
                }
            }
        }

        arsort($allergenCounts);
        return array_slice($allergenCounts, 0, 5, true);
    }

    /**
     * Helper method to get dietary options from menu items
     */
    private function getDietaryOptions($menuItems): array
    {
        $dietaryOptions = [];
        
        foreach ($menuItems as $item) {
            if ($item->dietary_info) {
                $dietaryInfo = json_decode($item->dietary_info, true);
                if (isset($dietaryInfo['dietary_tags'])) {
                    foreach ($dietaryInfo['dietary_tags'] as $tag) {
                        $dietaryOptions[$tag] = ($dietaryOptions[$tag] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($dietaryOptions);
        return $dietaryOptions;
    }
}
