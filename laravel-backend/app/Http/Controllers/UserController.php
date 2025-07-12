<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Allergen;
use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
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
            'allergies' => $user->allergies,
            'allergens' => $user->allergens,
            'mealPlans' => $user->mealPlans,
            'dietaryRestrictions' => $user->dietary_restrictions,
            'cuisinePreferences' => $user->cuisine_preferences,
            'preferredMealTimes' => $user->preferred_meal_times,
            'location' => $user->location,
            'preferences' => $user->preferences,
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
        
        $validator = Validator::make($request->all(), [
            'displayName' => 'string|max:255',
            'username' => 'string|max:255|unique:users,username,' . $user->id,
            'phoneNumber' => 'string|max:20',
            'address' => 'string|max:500',
            'age' => 'integer|min:1|max:120',
            'height' => 'numeric|min:50|max:300',
            'weight' => 'numeric|min:20|max:500',
            'activityLevel' => 'in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'fitnessGoal' => 'in:lose_weight,maintain_weight,gain_weight,build_muscle'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        if ($request->has('displayName')) {
            $updateData['name'] = $request->displayName;
        }
        if ($request->has('username')) {
            $updateData['username'] = $request->username;
        }
        if ($request->has('phoneNumber')) {
            $updateData['phone_number'] = $request->phoneNumber;
        }
        if ($request->has('address')) {
            $updateData['address'] = $request->address;
        }
        if ($request->has('age')) {
            $updateData['age'] = $request->age;
        }
        if ($request->has('height')) {
            $updateData['height'] = $request->height;
        }
        if ($request->has('weight')) {
            $updateData['weight'] = $request->weight;
        }
        if ($request->has('activityLevel')) {
            $updateData['activity_level'] = $request->activityLevel;
        }
        if ($request->has('fitnessGoal')) {
            $updateData['fitness_goal'] = $request->fitnessGoal;
        }

        $user->update($updateData);

        return response()->json($this->getProfile($request)->getData());
    }

    /**
     * Upload user photo
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Delete old photo if exists
        if ($user->photo_url) {
            Storage::delete(str_replace('/storage/', '', $user->photo_url));
        }

        // Store new photo
        $path = $request->file('photo')->store('user-photos', 'public');
        $photoURL = '/storage/' . $path;

        $user->update(['photo_url' => $photoURL]);

        return response()->json([
            'photoURL' => $photoURL
        ]);
    }

    /**
     * Get nutritional preferences
     */
    public function getNutritionalPreferences(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'allergies' => $user->allergies ?? [],
            'dietaryRestrictions' => $user->dietary_restrictions ?? [],
            'cuisinePreferences' => $user->cuisine_preferences ?? [],
            'preferredMealTimes' => $user->preferred_meal_times ?? [],
            'preferences' => $user->preferences ?? []
        ]);
    }

    /**
     * Add allergen
     */
    public function addAllergen(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'severity' => 'in:mild,moderate,severe',
            'notes' => 'string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $allergen = Allergen::create([
            'user_id' => $userId,
            'name' => $request->name,
            'category' => $request->category,
            'severity' => $request->severity,
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Allergen added successfully',
            'allergen' => $allergen
        ], 201);
    }

    /**
     * Remove allergen
     */
    public function removeAllergen(Request $request, $userId, $allergenId): JsonResponse
    {
        $allergen = Allergen::where('user_id', $userId)->findOrFail($allergenId);
        $allergen->delete();

        return response()->json([
            'message' => 'Allergen removed successfully'
        ]);
    }

    /**
     * Add meal plan
     */
    public function addMealPlan(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'string|max:500',
            'duration' => 'integer|min:1',
            'calories_per_day' => 'integer|min:1',
            'type' => 'in:weight_loss,muscle_gain,maintenance,custom',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'meals' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $mealPlan = MealPlan::create([
            'user_id' => $userId,
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration ?? 7,
            'calories_per_day' => $request->calories_per_day ?? 2000,
            'type' => $request->type ?? 'custom',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? false,
            'meals' => $request->meals ?? []
        ]);

        return response()->json([
            'message' => 'Meal plan added successfully',
            'meal_plan' => $mealPlan
        ], 201);
    }

    /**
     * Remove meal plan
     */
    public function removeMealPlan(Request $request, $userId, $mealPlanId): JsonResponse
    {
        $mealPlan = MealPlan::where('user_id', $userId)->findOrFail($mealPlanId);
        $mealPlan->delete();

        return response()->json([
            'message' => 'Meal plan removed successfully'
        ]);
    }

    /**
     * Set active meal plan
     */
    public function setActiveMealPlan(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mealPlanId' => 'required|exists:meal_plans,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Deactivate all meal plans for this user
        MealPlan::where('user_id', $userId)->update(['is_active' => false]);

        // Activate the selected meal plan
        $mealPlan = MealPlan::where('user_id', $userId)->findOrFail($request->mealPlanId);
        $mealPlan->update(['is_active' => true]);

        return response()->json([
            'message' => 'Active meal plan set successfully',
            'meal_plan' => $mealPlan
        ]);
    }
}
