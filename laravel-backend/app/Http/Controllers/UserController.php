<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['allergens', 'mealPlans']);
        
        return response()->json([
            'success' => true,
            'uid' => $user->id,
            'email' => $user->email,
            'displayName' => $user->name,
            'username' => $user->username,
            'phoneNumber' => $user->phone_number,
            'address' => $user->address,
            'applicationStatus' => $user->application_status,
            'role' => $user->role,
            'photoURL' => $user->photo_url,
            'age' => $user->age,
            'height' => $user->height,
            'weight' => $user->weight,
            'activityLevel' => $user->activity_level,
            'fitnessGoal' => $user->fitness_goal,
            'allergies' => $user->allergies ?? [],
            'allergens' => $user->allergens->map(function($allergen) {
                return [
                    'id' => $allergen->id,
                    'name' => $allergen->name,
                    'category' => $allergen->category,
                    'severity' => $allergen->severity,
                    'notes' => $allergen->notes,
                    'addedAt' => $allergen->created_at
                ];
            }),
            'mealPlans' => $user->mealPlans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'isActive' => $plan->is_active,
                    'meals' => $plan->meals ?? []
                ];
            }),
            'dietaryRestrictions' => $user->dietary_restrictions ?? [],
            'cuisinePreferences' => $user->cuisine_preferences ?? [],
            'preferredMealTimes' => $user->preferred_meal_times ?? [],
            'location' => $user->location ?? [],
            'preferences' => $user->preferences ?? [],
            'createdAt' => $user->created_at,
            'updatedAt' => $user->updated_at
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validatedData = $request->validate([
            'displayName' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'phoneNumber' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'age' => 'sometimes|integer|min:13|max:120',
            'height' => 'sometimes|numeric|min:100|max:300',
            'weight' => 'sometimes|numeric|min:30|max:300',
            'activityLevel' => 'sometimes|in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'fitnessGoal' => 'sometimes|in:lose_weight,maintain_weight,gain_weight,build_muscle',
            'allergies' => 'sometimes|array',
            'dietaryRestrictions' => 'sometimes|array',
            'cuisinePreferences' => 'sometimes|array',
            'preferredMealTimes' => 'sometimes|array',
            'location' => 'sometimes|array',
            'preferences' => 'sometimes|array'
        ]);

        // Map frontend field names to database field names
        $updateData = [];
        if (isset($validatedData['displayName'])) {
            $updateData['name'] = $validatedData['displayName'];
        }
        if (isset($validatedData['phoneNumber'])) {
            $updateData['phone_number'] = $validatedData['phoneNumber'];
        }
        if (isset($validatedData['activityLevel'])) {
            $updateData['activity_level'] = $validatedData['activityLevel'];
        }
        if (isset($validatedData['fitnessGoal'])) {
            $updateData['fitness_goal'] = $validatedData['fitnessGoal'];
        }
        if (isset($validatedData['dietaryRestrictions'])) {
            $updateData['dietary_restrictions'] = $validatedData['dietaryRestrictions'];
        }
        if (isset($validatedData['cuisinePreferences'])) {
            $updateData['cuisine_preferences'] = $validatedData['cuisinePreferences'];
        }
        if (isset($validatedData['preferredMealTimes'])) {
            $updateData['preferred_meal_times'] = $validatedData['preferredMealTimes'];
        }

        // Direct mapping fields
        $directFields = ['username', 'address', 'age', 'height', 'weight', 'allergies', 'location', 'preferences'];
        foreach ($directFields as $field) {
            if (isset($validatedData[$field])) {
                $updateData[$field] = $validatedData[$field];
            }
        }

        $user->update($updateData);
        $user->refresh();
        $user->load(['allergens', 'mealPlans']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'uid' => $user->id,
            'email' => $user->email,
            'displayName' => $user->name,
            'username' => $user->username,
            'phoneNumber' => $user->phone_number,
            'address' => $user->address,
            'applicationStatus' => $user->application_status,
            'role' => $user->role,
            'photoURL' => $user->photo_url,
            'age' => $user->age,
            'height' => $user->height,
            'weight' => $user->weight,
            'activityLevel' => $user->activity_level,
            'fitnessGoal' => $user->fitness_goal,
            'allergies' => $user->allergies ?? [],
            'allergens' => $user->allergens->map(function($allergen) {
                return [
                    'id' => $allergen->id,
                    'name' => $allergen->name,
                    'category' => $allergen->category,
                    'severity' => $allergen->severity,
                    'notes' => $allergen->notes,
                    'addedAt' => $allergen->created_at
                ];
            }),
            'mealPlans' => $user->mealPlans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'isActive' => $plan->is_active,
                    'meals' => $plan->meals ?? []
                ];
            }),
            'dietaryRestrictions' => $user->dietary_restrictions ?? [],
            'cuisinePreferences' => $user->cuisine_preferences ?? [],
            'preferredMealTimes' => $user->preferred_meal_times ?? [],
            'location' => $user->location ?? [],
            'preferences' => $user->preferences ?? [],
            'createdAt' => $user->created_at,
            'updatedAt' => $user->updated_at
        ]);
    }

    /**
     * Upload user photo
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = $request->user();
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');
            
            $photoURL = asset('storage/' . $path);
            
            $user->update(['photo_url' => $photoURL]);
            
            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'photoURL' => $photoURL
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo file provided'
        ], 400);
    }

    /**
     * Get nutritional preferences
     */
    public function getNutritionalPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'preferences' => [
                'activityLevel' => $user->activity_level,
                'fitnessGoal' => $user->fitness_goal,
                'allergies' => $user->allergies ?? [],
                'dietaryRestrictions' => $user->dietary_restrictions ?? [],
                'cuisinePreferences' => $user->cuisine_preferences ?? [],
                'preferredMealTimes' => $user->preferred_meal_times ?? [],
                'maxCookingTime' => $user->preferences['maxCookingTime'] ?? null,
                'skillLevel' => $user->preferences['skillLevel'] ?? null,
                'budget' => $user->preferences['budget'] ?? null,
                'servingSize' => $user->preferences['servingSize'] ?? null
            ]
        ]);
    }

    /**
     * Update nutritional preferences
     */
    public function updateNutritionalPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validatedData = $request->validate([
            'activityLevel' => 'sometimes|in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'fitnessGoal' => 'sometimes|in:lose_weight,maintain_weight,gain_weight,build_muscle',
            'allergies' => 'sometimes|array',
            'dietaryRestrictions' => 'sometimes|array',
            'cuisinePreferences' => 'sometimes|array',
            'preferredMealTimes' => 'sometimes|array',
            'maxCookingTime' => 'sometimes|integer|min:5|max:300',
            'skillLevel' => 'sometimes|in:beginner,intermediate,advanced',
            'budget' => 'sometimes|in:low,medium,high',
            'servingSize' => 'sometimes|integer|min:1|max:12'
        ]);

        $updateData = [];
        
        if (isset($validatedData['activityLevel'])) {
            $updateData['activity_level'] = $validatedData['activityLevel'];
        }
        if (isset($validatedData['fitnessGoal'])) {
            $updateData['fitness_goal'] = $validatedData['fitnessGoal'];
        }
        if (isset($validatedData['allergies'])) {
            $updateData['allergies'] = $validatedData['allergies'];
        }
        if (isset($validatedData['dietaryRestrictions'])) {
            $updateData['dietary_restrictions'] = $validatedData['dietaryRestrictions'];
        }
        if (isset($validatedData['cuisinePreferences'])) {
            $updateData['cuisine_preferences'] = $validatedData['cuisinePreferences'];
        }
        if (isset($validatedData['preferredMealTimes'])) {
            $updateData['preferred_meal_times'] = $validatedData['preferredMealTimes'];
        }

        // Update preferences object
        $preferences = $user->preferences ?? [];
        $preferencesFields = ['maxCookingTime', 'skillLevel', 'budget', 'servingSize'];
        foreach ($preferencesFields as $field) {
            if (isset($validatedData[$field])) {
                $preferences[$field] = $validatedData[$field];
            }
        }
        if (!empty($preferences)) {
            $updateData['preferences'] = $preferences;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Nutritional preferences updated successfully',
            'preferences' => [
                'activityLevel' => $user->activity_level,
                'fitnessGoal' => $user->fitness_goal,
                'allergies' => $user->allergies ?? [],
                'dietaryRestrictions' => $user->dietary_restrictions ?? [],
                'cuisinePreferences' => $user->cuisine_preferences ?? [],
                'preferredMealTimes' => $user->preferred_meal_times ?? [],
                'maxCookingTime' => $user->preferences['maxCookingTime'] ?? null,
                'skillLevel' => $user->preferences['skillLevel'] ?? null,
                'budget' => $user->preferences['budget'] ?? null,
                'servingSize' => $user->preferences['servingSize'] ?? null
            ]
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Delete related data
        $user->allergens()->delete();
        $user->mealPlans()->delete();
        $user->tokens()->delete();
        
        // Delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    /**
     * Add allergen
     */
    public function addAllergen(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'severity' => 'sometimes|in:mild,moderate,severe',
            'notes' => 'sometimes|string|max:500'
        ]);

        $allergen = $user->allergens()->create([
            'name' => $validatedData['name'],
            'category' => $validatedData['category'],
            'severity' => $validatedData['severity'] ?? 'mild',
            'notes' => $validatedData['notes'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Allergen added successfully',
            'allergen' => [
                'id' => $allergen->id,
                'name' => $allergen->name,
                'category' => $allergen->category,
                'severity' => $allergen->severity,
                'notes' => $allergen->notes,
                'addedAt' => $allergen->created_at
            ]
        ]);
    }

    /**
     * Remove allergen
     */
    public function removeAllergen(Request $request): JsonResponse
    {
        $user = $request->user();
        $allergenId = $request->route('allergenId');
        
        $allergen = $user->allergens()->find($allergenId);
        
        if (!$allergen) {
            return response()->json([
                'success' => false,
                'message' => 'Allergen not found'
            ], 404);
        }

        $allergen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Allergen removed successfully'
        ]);
    }

    /**
     * Add meal plan
     */
    public function addMealPlan(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string|max:500',
            'meals' => 'sometimes|array',
            'isActive' => 'sometimes|boolean'
        ]);

        // If this is set to active, deactivate other meal plans
        if ($validatedData['isActive'] ?? false) {
            $user->mealPlans()->update(['is_active' => false]);
        }

        $mealPlan = $user->mealPlans()->create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'meals' => $validatedData['meals'] ?? [],
            'is_active' => $validatedData['isActive'] ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meal plan added successfully',
            'mealPlan' => [
                'id' => $mealPlan->id,
                'name' => $mealPlan->name,
                'description' => $mealPlan->description,
                'isActive' => $mealPlan->is_active,
                'meals' => $mealPlan->meals ?? []
            ]
        ]);
    }

    /**
     * Remove meal plan
     */
    public function removeMealPlan(Request $request): JsonResponse
    {
        $user = $request->user();
        $mealPlanId = $request->route('mealPlanId');
        
        $mealPlan = $user->mealPlans()->find($mealPlanId);
        
        if (!$mealPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Meal plan not found'
            ], 404);
        }

        $mealPlan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meal plan removed successfully'
        ]);
    }

    /**
     * Set active meal plan
     */
    public function setActiveMealPlan(Request $request): JsonResponse
    {
        $user = $request->user();
        $mealPlanId = $request->input('mealPlanId');
        
        $mealPlan = $user->mealPlans()->find($mealPlanId);
        
        if (!$mealPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Meal plan not found'
            ], 404);
        }

        // Deactivate all other meal plans
        $user->mealPlans()->update(['is_active' => false]);
        
        // Activate the selected meal plan
        $mealPlan->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Active meal plan set successfully',
            'mealPlan' => [
                'id' => $mealPlan->id,
                'name' => $mealPlan->name,
                'description' => $mealPlan->description,
                'isActive' => $mealPlan->is_active,
                'meals' => $mealPlan->meals ?? []
            ]
        ]);
    }

    /**
     * Get user favorites
     */
    public function getFavorites(Request $request): JsonResponse
    {
        $user = $request->user();
        $favorites = \App\Models\Favorite::with(['menuItem.karenderia'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($favorite) {
                return [
                    'id' => $favorite->id,
                    'userId' => $favorite->user_id,
                    'menuItemId' => $favorite->menu_item_id,
                    'karenderiaId' => $favorite->menuItem->karenderia_id,
                    'menuItemName' => $favorite->menuItem->name,
                    'menuItemImage' => $favorite->menuItem->image,
                    'menuItemPrice' => $favorite->menuItem->price,
                    'karenderiaName' => $favorite->menuItem->karenderia->name ?? 'Unknown',
                    'addedAt' => $favorite->created_at
                ];
            });

        return response()->json($favorites);
    }

    /**
     * Add item to favorites
     */
    public function addFavorite(Request $request): JsonResponse
    {
        $request->validate([
            'menuItemId' => 'required|exists:menu_items,id',
            'karenderiaId' => 'required|exists:karenderias,id'
        ]);

        $user = $request->user();
        
        // Check if already in favorites
        $existing = \App\Models\Favorite::where([
            'user_id' => $user->id,
            'menu_item_id' => $request->menuItemId
        ])->first();

        if ($existing) {
            return response()->json(['message' => 'Item already in favorites'], 409);
        }

        $favorite = \App\Models\Favorite::create([
            'user_id' => $user->id,
            'menu_item_id' => $request->menuItemId
        ]);

        $favorite->load('menuItem.karenderia');

        return response()->json([
            'id' => $favorite->id,
            'userId' => $favorite->user_id,
            'menuItemId' => $favorite->menu_item_id,
            'karenderiaId' => $favorite->menuItem->karenderia_id,
            'menuItemName' => $favorite->menuItem->name,
            'menuItemImage' => $favorite->menuItem->image,
            'menuItemPrice' => $favorite->menuItem->price,
            'karenderiaName' => $favorite->menuItem->karenderia->name ?? 'Unknown',
            'addedAt' => $favorite->created_at
        ]);
    }

    /**
     * Remove item from favorites
     */
    public function removeFavorite(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $favorite = \App\Models\Favorite::where([
            'id' => $id,
            'user_id' => $user->id
        ])->first();

        if (!$favorite) {
            return response()->json(['error' => 'Favorite not found'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }

    /**
     * Get user meal history
     */
    public function getMealHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $history = \App\Models\MealHistory::with(['menuItem.karenderia'])
            ->where('user_id', $user->id)
            ->orderBy('ordered_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'userId' => $item->user_id,
                    'menuItemId' => $item->menu_item_id,
                    'karenderiaId' => $item->menuItem->karenderia_id,
                    'menuItemName' => $item->menuItem->name,
                    'menuItemImage' => $item->menuItem->image,
                    'menuItemPrice' => $item->menuItem->price,
                    'karenderiaName' => $item->menuItem->karenderia->name ?? 'Unknown',
                    'orderedAt' => $item->ordered_at,
                    'quantity' => $item->quantity,
                    'rating' => $item->rating,
                    'review' => $item->review
                ];
            });

        return response()->json($history);
    }

    /**
     * Add item to meal history (called when order is completed)
     */
    public function addMealHistory(Request $request): JsonResponse
    {
        $request->validate([
            'menuItemId' => 'required|exists:menu_items,id',
            'karenderiaId' => 'required|exists:karenderias,id',
            'quantity' => 'required|integer|min:1',
            'orderId' => 'required|string'
        ]);

        $user = $request->user();

        $history = \App\Models\MealHistory::create([
            'user_id' => $user->id,
            'menu_item_id' => $request->menuItemId,
            'quantity' => $request->quantity,
            'order_id' => $request->orderId,
            'ordered_at' => now()
        ]);

        $history->load('menuItem.karenderia');

        return response()->json([
            'id' => $history->id,
            'userId' => $history->user_id,
            'menuItemId' => $history->menu_item_id,
            'karenderiaId' => $history->menuItem->karenderia_id,
            'menuItemName' => $history->menuItem->name,
            'menuItemImage' => $history->menuItem->image,
            'menuItemPrice' => $history->menuItem->price,
            'karenderiaName' => $history->menuItem->karenderia->name ?? 'Unknown',
            'orderedAt' => $history->ordered_at,
            'quantity' => $history->quantity,
            'rating' => $history->rating,
            'review' => $history->review
        ]);
    }

    /**
     * Add review to meal history item
     */
    public function addMealReview(Request $request, $id): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();
        
        $history = \App\Models\MealHistory::where([
            'id' => $id,
            'user_id' => $user->id
        ])->first();

        if (!$history) {
            return response()->json(['error' => 'Meal history item not found'], 404);
        }

        $history->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return response()->json([
            'message' => 'Review added successfully',
            'rating' => $history->rating,
            'review' => $history->review
        ]);
    }
}
