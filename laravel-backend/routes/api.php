<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuItemController;
use App\Models\User;

// EMERGENCY LOGIN FOR PRESENTATION
Route::post('/emergency-login', function (Request $request) {
    $user = User::where('email', 'alica@gmail.com')->first();
    
    if ($user && $user->role === 'karenderia_owner') {
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'displayName' => $user->name,
                'role' => $user->role,
                'verified' => $user->verified
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'karenderia' => [
                'id' => $user->karenderia->id,
                'business_name' => $user->karenderia->business_name,
                'status' => $user->karenderia->status,
                'approved_at' => $user->karenderia->approved_at->format('M d, Y')
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
    
    return response()->json(['message' => 'User not found'], 404);
});
use App\Http\Controllers\DailyMenuController;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'Laravel backend is running!', 'timestamp' => now()]);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register-karenderia-owner', [AuthController::class, 'registerKarenderiaOwner']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// User profile routes
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/upload-photo', [UserController::class, 'uploadPhoto']);
    Route::get('/nutritional-preferences', [UserController::class, 'getNutritionalPreferences']);
    Route::put('/nutritional-preferences', [UserController::class, 'updateNutritionalPreferences']);
    Route::delete('/account', [UserController::class, 'deleteAccount']);
});

// User-specific routes (allergens, meal plans)
Route::middleware('auth:sanctum')->prefix('users/{userId}')->group(function () {
    Route::post('/allergens', [UserController::class, 'addAllergen']);
    Route::delete('/allergens/{allergenId}', [UserController::class, 'removeAllergen']);
    Route::post('/meal-plans', [UserController::class, 'addMealPlan']);
    Route::delete('/meal-plans/{mealPlanId}', [UserController::class, 'removeMealPlan']);
    Route::put('/active-meal-plan', [UserController::class, 'setActiveMealPlan']);
});

// Karenderia routes
Route::prefix('karenderias')->group(function () {
    Route::get('/', [KarenderiaController::class, 'index']);
    Route::get('/search', [KarenderiaController::class, 'search']);
    
    // Protected routes for karenderia owners (must come before {id} route)
    Route::get('/nearby', [KarenderiaController::class, 'nearby']);
    
    // Protected routes for karenderia owners (must come before {id} route)
    Route::get('/nearby', [KarenderiaController::class, 'nearby']);
    
    // Protected routes for karenderia owners (must come before {id} route)
    Route::middleware(['auth:sanctum', 'karenderia.approved'])->group(function () {
        Route::post('/', [KarenderiaController::class, 'store']);
        Route::get('/my-karenderia', [KarenderiaController::class, 'myKarenderia']);
        Route::put('/{id}', [KarenderiaController::class, 'update']);
        Route::put('/{id}/data', [KarenderiaController::class, 'updateKarenderiaData']);
        Route::delete('/{id}', [KarenderiaController::class, 'destroy']);
    });
    
    // Dynamic ID route must come AFTER specific routes to avoid conflicts
    // Dynamic ID route must come AFTER specific routes to avoid conflicts
    Route::get('/{id}', [KarenderiaController::class, 'show']);
});

// Meal Plan routes
Route::prefix('meal-plans')->group(function () {
    Route::get('/', [MealPlanController::class, 'index']);
    Route::post('/', [MealPlanController::class, 'store']);
    Route::get('/{id}', [MealPlanController::class, 'show']);
    Route::put('/{id}', [MealPlanController::class, 'update']);
    Route::delete('/{id}', [MealPlanController::class, 'destroy']);
});

// Public menu routes for customers (must come BEFORE protected routes)
Route::prefix('menu-items')->group(function () {
    Route::get('/search', [MenuItemController::class, 'searchByKarenderia']); // Public endpoint for customers
    Route::get('/{id}/public', [MenuItemController::class, 'showPublic']); // Public endpoint for menu item details
});

// Menu Items routes
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('menu-items')->group(function () {
    Route::post('/', [MenuItemController::class, 'store']);
    Route::get('/', [MenuItemController::class, 'index']);
    Route::get('/search', [MenuItemController::class, 'search']);
    Route::get('/my-menu', [MenuItemController::class, 'myMenuItems']);
    Route::get('/allergen-summary', [MenuItemController::class, 'getAllergenSummary']);
    Route::get('/my-menu', [MenuItemController::class, 'getMyMenuItems']); // Added missing route
    Route::get('/{id}', [MenuItemController::class, 'show']);
    Route::put('/{id}', [MenuItemController::class, 'update']);
    Route::put('/{id}/nutrition', [MenuItemController::class, 'updateNutrition']);
    Route::put('/{id}/availability', [MenuItemController::class, 'updateAvailability']);
    Route::delete('/{id}', [MenuItemController::class, 'destroy']);
});

// Daily Menu routes (Menu of the Day)
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('daily-menu')->group(function () {
    // For Karenderia Owners
    Route::get('/', [DailyMenuController::class, 'index']); // Get owner's daily menu
    Route::post('/', [DailyMenuController::class, 'store']); // Add menu item to daily menu
    Route::put('/{id}', [DailyMenuController::class, 'update']); // Update daily menu entry
    Route::delete('/{id}', [DailyMenuController::class, 'destroy']); // Remove from daily menu
    Route::get('/available-items', [DailyMenuController::class, 'getAvailableMenuItems']); // Get menu items for selection
});

// Daily Menu public routes (for customers)
Route::prefix('daily-menu')->group(function () {
    Route::get('/available', [DailyMenuController::class, 'getAvailableForCustomers']); // Get available karenderias by meal type
});

// Inventory routes (for karenderia owners to manage ingredients/supplies)
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index']); // Get inventory items
    Route::post('/', [InventoryController::class, 'store']); // Add inventory item
    Route::get('/{id}', [InventoryController::class, 'show']); // Get specific inventory item
    Route::put('/{id}', [InventoryController::class, 'update']); // Update inventory item
    Route::delete('/{id}', [InventoryController::class, 'destroy']); // Delete inventory item
    Route::post('/{id}/use', [InventoryController::class, 'useIngredient']); // Use ingredient in cooking
    Route::post('/{id}/restock', [InventoryController::class, 'restock']); // Restock ingredient
});

// Menu Categories (for organizing menu items)
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('menu-categories')->group(function () {
    Route::get('/', [MenuCategoryController::class, 'index']); // Get categories for owner's karenderia
    Route::post('/', [MenuCategoryController::class, 'store']); // Create new category
    Route::get('/{id}', [MenuCategoryController::class, 'show']); // Get specific category
    Route::put('/{id}', [MenuCategoryController::class, 'update']); // Update category
    Route::delete('/{id}', [MenuCategoryController::class, 'destroy']); // Delete category
});

// Ingredients routes (for managing ingredient database)
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('ingredients')->group(function () {
    Route::get('/', [IngredientController::class, 'index']); // Get all ingredients
    Route::post('/', [IngredientController::class, 'store']); // Add new ingredient
    Route::get('/{id}', [IngredientController::class, 'show']); // Get specific ingredient
    Route::put('/{id}', [IngredientController::class, 'update']); // Update ingredient
    Route::delete('/{id}', [IngredientController::class, 'destroy']); // Delete ingredient
});

// Analytics routes for karenderia owners
Route::middleware(['auth:sanctum', 'karenderia.approved'])->prefix('analytics')->group(function () {
    Route::get('/daily-sales', [MenuItemController::class, 'getDailySales']);
    Route::get('/monthly-sales', [MenuItemController::class, 'getMonthlySales']);
    Route::get('/sales-summary', [MenuItemController::class, 'getSalesSummary']);
});

// Admin routes (Protected - Admin only)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    
    // Karenderias Management
    Route::get('/karenderias', [AdminController::class, 'getAllKarenderias']);
    Route::get('/karenderias/{id}', [AdminController::class, 'getKarenderiaById']);
    Route::put('/karenderias/{id}', [AdminController::class, 'updateKarenderiaDetails']);
    Route::put('/karenderias/{id}/location', [AdminController::class, 'updateKarenderiaLocation']);
    Route::put('/karenderias/{id}/status', [AdminController::class, 'updateKarenderiaStatus']);
    Route::delete('/karenderias/{id}', [AdminController::class, 'deleteKarenderia']);
    Route::get('/karenderias/{id}/inventory', [AdminController::class, 'karenderiaInventory']);
    
    // Legacy routes (keeping for backward compatibility)
    Route::get('/karenderias-old', [AdminController::class, 'karenderias']);
    Route::get('/karenderias-old/{id}', [AdminController::class, 'karenderiaDetails']);
    
    // Menu Items Management - All menu items across all karenderias
    Route::get('/menu-items', [AdminController::class, 'allMenuItems']);
    
    // Sales Analytics
    Route::get('/sales-analytics', [AdminController::class, 'salesAnalytics']);
    
    // Inventory Management
    Route::get('/inventory/alerts', [AdminController::class, 'inventoryAlerts']);
    
    // User Management
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/customers', [AdminController::class, 'getCustomers']);
    Route::get('/karenderia-owners', [AdminController::class, 'getKarenderiaOwners']);
    Route::put('/users/{userId}/role', [AdminController::class, 'updateUserRole']);
    Route::put('/users/{userId}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    Route::delete('/users/{userId}', [AdminController::class, 'deleteUser']);
});

// Report routes
Route::apiResource('reports', ReportController::class);

// Fallback route for undefined API routes
Route::fallback(function () {
    return response()->json(['message' => 'API route not found'], 404);
});
