<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Recipe;
use App\Models\MenuItem;
use App\Models\Karenderia;

class RecipeController extends Controller
{
    /**
     * Get all recipes for a karenderia owner
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user'
                ], 404);
            }

            $recipes = Recipe::where('karenderia_id', $karenderia->id)
                ->with('menuItems')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($recipe) {
                    return [
                        'id' => $recipe->id,
                        'name' => $recipe->name,
                        'description' => $recipe->description,
                        'category' => $recipe->category,
                        'cuisine_type' => $recipe->cuisine_type,
                        'difficulty_level' => $recipe->difficulty_level,
                        'difficulty_color' => $recipe->difficulty_color,
                        'total_time' => $recipe->total_time,
                        'servings' => $recipe->servings,
                        'cost_estimate' => $recipe->cost_estimate,
                        'cost_per_serving' => $recipe->cost_per_serving,
                        'ingredients_count' => count($recipe->ingredients),
                        'instructions_count' => count($recipe->instructions),
                        'is_published' => $recipe->is_published,
                        'is_signature' => $recipe->is_signature,
                        'rating' => $recipe->rating,
                        'total_reviews' => $recipe->total_reviews,
                        'times_cooked' => $recipe->times_cooked,
                        'can_create_menu_item' => $recipe->canCreateMenuItem(),
                        'has_menu_item' => $recipe->menuItems->count() > 0,
                        'menu_items' => $recipe->menuItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'price' => $item->price,
                                'is_available' => $item->is_available
                            ];
                        }),
                        'created_at' => $recipe->created_at,
                        'updated_at' => $recipe->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recipes,
                'meta' => [
                    'total' => $recipes->count(),
                    'published' => $recipes->where('is_published', true)->count(),
                    'with_menu_items' => $recipes->where('has_menu_item', true)->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recipes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific recipe with full details
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user'
                ], 404);
            }

            $recipe = Recipe::where('id', $id)
                ->where('karenderia_id', $karenderia->id)
                ->with('menuItems')
                ->first();

            if (!$recipe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recipe not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'description' => $recipe->description,
                    'ingredients' => $recipe->ingredients,
                    'instructions' => $recipe->instructions,
                    'prep_time_minutes' => $recipe->prep_time_minutes,
                    'cook_time_minutes' => $recipe->cook_time_minutes,
                    'total_time' => $recipe->total_time,
                    'difficulty_level' => $recipe->difficulty_level,
                    'difficulty_color' => $recipe->difficulty_color,
                    'servings' => $recipe->servings,
                    'category' => $recipe->category,
                    'cuisine_type' => $recipe->cuisine_type,
                    'cost_estimate' => $recipe->cost_estimate,
                    'cost_per_serving' => $recipe->cost_per_serving,
                    'nutritional_info' => $recipe->nutritional_info,
                    'is_published' => $recipe->is_published,
                    'is_signature' => $recipe->is_signature,
                    'rating' => $recipe->rating,
                    'total_reviews' => $recipe->total_reviews,
                    'times_cooked' => $recipe->times_cooked,
                    'can_create_menu_item' => $recipe->canCreateMenuItem(),
                    'menu_items' => $recipe->menuItems,
                    'created_at' => $recipe->created_at,
                    'updated_at' => $recipe->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recipe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new recipe
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'ingredients' => 'required|array',
                'instructions' => 'required|array',
                'prep_time_minutes' => 'required|integer|min:1',
                'cook_time_minutes' => 'required|integer|min:1',
                'difficulty_level' => 'required|in:easy,medium,hard',
                'servings' => 'required|integer|min:1',
                'category' => 'required|string',
                'cuisine_type' => 'required|string',
                'cost_estimate' => 'required|numeric|min:0',
                'nutritional_info' => 'nullable|array',
                'is_signature' => 'boolean'
            ]);

            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user'
                ], 404);
            }

            $recipe = Recipe::create([
                'karenderia_id' => $karenderia->id,
                ...$validatedData,
                'is_published' => true // Auto-publish new recipes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recipe created successfully',
                'data' => $recipe
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create recipe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create menu item from recipe
     */
    public function createMenuItem(Request $request, $recipeId): JsonResponse
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user'
                ], 404);
            }

            $recipe = Recipe::where('id', $recipeId)
                ->where('karenderia_id', $karenderia->id)
                ->first();

            if (!$recipe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recipe not found'
                ], 404);
            }

            if (!$recipe->canCreateMenuItem()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recipe does not have enough details to create menu item'
                ], 400);
            }

            // Check if menu item already exists for this recipe
            $existingMenuItem = MenuItem::where('recipe_id', $recipe->id)->first();
            if ($existingMenuItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item already exists for this recipe',
                    'data' => $existingMenuItem
                ], 409);
            }

            $validatedData = $request->validate([
                'price' => 'required|numeric|min:0',
                'is_featured' => 'boolean',
                'spice_level' => 'integer|min:0|max:5'
            ]);

            // Create menu item from recipe
            $menuItem = MenuItem::create([
                'karenderia_id' => $karenderia->id,
                'recipe_id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description,
                'price' => $validatedData['price'],
                'cost_price' => $recipe->cost_per_serving,
                'category' => $recipe->category,
                'is_available' => true,
                'is_featured' => $validatedData['is_featured'] ?? false,
                'preparation_time_minutes' => $recipe->total_time,
                'calories' => $recipe->nutritional_info['calories_per_serving'] ?? null,
                'ingredients' => array_keys($recipe->ingredients),
                'spice_level' => $validatedData['spice_level'] ?? 0,
                'average_rating' => 0,
                'total_reviews' => 0,
                'total_orders' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully from recipe',
                'data' => $menuItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu item from recipe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recipe statistics for dashboard
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $karenderia = Karenderia::where('owner_id', $user->id)->first();
            
            if (!$karenderia) {
                return response()->json([
                    'success' => false,
                    'message' => 'No karenderia found for this user'
                ], 404);
            }

            $totalRecipes = Recipe::where('karenderia_id', $karenderia->id)->count();
            $publishedRecipes = Recipe::where('karenderia_id', $karenderia->id)->published()->count();
            $signatureRecipes = Recipe::where('karenderia_id', $karenderia->id)->signature()->count();
            $recipesWithMenuItems = Recipe::where('karenderia_id', $karenderia->id)
                ->whereHas('menuItems')
                ->count();

            $categoryCounts = Recipe::where('karenderia_id', $karenderia->id)
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category');

            $difficultyCounts = Recipe::where('karenderia_id', $karenderia->id)
                ->selectRaw('difficulty_level, COUNT(*) as count')
                ->groupBy('difficulty_level')
                ->pluck('count', 'difficulty_level');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_recipes' => $totalRecipes,
                    'published_recipes' => $publishedRecipes,
                    'signature_recipes' => $signatureRecipes,
                    'recipes_with_menu_items' => $recipesWithMenuItems,
                    'ready_for_menu' => $totalRecipes - $recipesWithMenuItems,
                    'category_breakdown' => $categoryCounts,
                    'difficulty_breakdown' => $difficultyCounts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recipe statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
