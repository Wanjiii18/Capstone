<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\NutritionController;

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

// Order routes
Route::prefix('orders')->group(function () {
    // Guest order creation (no auth required)
    Route::post('/', [OrderController::class, 'store']);
    
    // Authenticated order routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/recent', [OrderController::class, 'getRecentOrders']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    });
});

// Karenderia routes
Route::prefix('karenderias')->group(function () {
    Route::get('/', [KarenderiaController::class, 'index']);
    Route::get('/nearby', [KarenderiaController::class, 'nearby']);
    Route::get('/search', [KarenderiaController::class, 'search']);
    Route::get('/{id}', [KarenderiaController::class, 'show']);
    
    // Protected routes for karenderia owners
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-karenderia', [KarenderiaController::class, 'getMyKarenderia']);
        Route::post('/', [KarenderiaController::class, 'store']);
        Route::put('/{id}', [KarenderiaController::class, 'update']);
        Route::delete('/{id}', [KarenderiaController::class, 'destroy']);
    });
});

// Meal Plan routes
Route::prefix('meal-plans')->group(function () {
    Route::get('/', [MealPlanController::class, 'index']);
    Route::post('/', [MealPlanController::class, 'store']);
    Route::get('/{id}', [MealPlanController::class, 'show']);
    Route::put('/{id}', [MealPlanController::class, 'update']);
    Route::delete('/{id}', [MealPlanController::class, 'destroy']);
});

// Menu Items routes
Route::prefix('menu-items')->group(function () {
    // Public routes
    Route::get('/search', [MenuItemController::class, 'search']);
    Route::get('/{id}/details', [MenuItemController::class, 'getDetails']);
    Route::get('/', [MenuItemController::class, 'index']); // Public for customer browsing
    
    // Protected routes - require authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [MenuItemController::class, 'store']); // Require auth for creating menu items
        Route::get('/{id}', [MenuItemController::class, 'show']);
        Route::put('/{id}', [MenuItemController::class, 'update']);
        Route::delete('/{id}', [MenuItemController::class, 'destroy']);
        
        // Reviews
        Route::post('/{id}/reviews', [MenuItemController::class, 'addReview']);
        Route::get('/{id}/reviews', [MenuItemController::class, 'getReviews']);
    });
});

// Recipe Management routes
Route::middleware('auth:sanctum')->prefix('recipes')->group(function () {
    Route::get('/', [RecipeController::class, 'index']);
    Route::post('/', [RecipeController::class, 'store']);
    Route::get('/stats', [RecipeController::class, 'getStats']);
    Route::get('/{id}', [RecipeController::class, 'show']);
    Route::post('/{id}/create-menu-item', [RecipeController::class, 'createMenuItem']);
});

// User favorites and meal history routes
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Favorites
    Route::get('/favorites', [UserController::class, 'getFavorites']);
    Route::post('/favorites', [UserController::class, 'addFavorite']);
    Route::delete('/favorites/{id}', [UserController::class, 'removeFavorite']);
    
    // Meal History
    Route::get('/meal-history', [UserController::class, 'getMealHistory']);
    Route::post('/meal-history', [UserController::class, 'addMealHistory']);
    Route::put('/meal-history/{id}/review', [UserController::class, 'addMealReview']);
});

// Analytics routes for karenderia owners
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('/daily-sales', [MenuItemController::class, 'getDailySales']);
    Route::get('/monthly-sales', [MenuItemController::class, 'getMonthlySales']);
    Route::get('/sales-summary', [MenuItemController::class, 'getSalesSummary']);
});

// Admin routes (Protected - Admin only)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Karenderias Management
    Route::get('/karenderias', [AdminController::class, 'karenderias']);
    Route::get('/karenderias/{id}', [AdminController::class, 'karenderiaDetails']);
    Route::get('/karenderias/{id}/inventory', [AdminController::class, 'karenderiaInventory']);
    Route::put('/karenderias/{id}/status', [AdminController::class, 'updateKarenderiaStatus']);
    
    // Menu Items Management - All menu items across all karenderias
    Route::get('/menu-items', [AdminController::class, 'allMenuItems']);
    
    // Sales Analytics
    Route::get('/sales-analytics', [AdminController::class, 'salesAnalytics']);
    
    // Inventory Management
    Route::get('/inventory/alerts', [AdminController::class, 'inventoryAlerts']);
    
    // User Management
    Route::get('/users', [AdminController::class, 'users']);
});

// Nutrition routes
Route::prefix('nutrition')->group(function () {
    // Menu item nutrition management
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/menu-items/{menuItemId}', [NutritionController::class, 'updateMenuItemNutrition']);
        Route::post('/menu-items/bulk-update', [NutritionController::class, 'bulkUpdateNutrition']);
    });
    
    // Public nutrition data access
    Route::get('/menu-items/{menuItemId}', [NutritionController::class, 'getMenuItemNutrition']);
    Route::get('/search', [NutritionController::class, 'searchByNutrition']);
    Route::get('/karenderias/{karenderiaId}/stats', [NutritionController::class, 'getMenuNutritionStats']);
    
    // User-specific recommendations
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/recommendations', [NutritionController::class, 'getRecommendations']);
    });
});
