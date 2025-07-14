<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\MenuController;

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
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/recent', [OrderController::class, 'getRecentOrders']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
});

// Karenderia routes
Route::prefix('karenderias')->group(function () {
    Route::get('/', [KarenderiaController::class, 'index']);
    Route::get('/search', [KarenderiaController::class, 'search']);
    Route::get('/{id}', [KarenderiaController::class, 'show']);
    
    // Protected routes for karenderia owners
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [KarenderiaController::class, 'store']);
        Route::put('/{id}', [KarenderiaController::class, 'update']);
        Route::delete('/{id}', [KarenderiaController::class, 'destroy']);
    });
});

// Menu items routes
Route::prefix('menu-items')->group(function () {
    Route::get('/', [MenuController::class, 'index']);
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::post('/', [MenuController::class, 'store']); // Made public for testing
    
    // Protected routes for karenderia owners
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/{id}', [MenuController::class, 'update']);
        Route::delete('/{id}', [MenuController::class, 'destroy']);
        Route::patch('/{id}/toggle-availability', [MenuController::class, 'toggleAvailability']);
    });
});

// Quick fix for missing endpoints your app is calling
Route::get('/menu-categories', function () {
    return response()->json([
        'success' => true,
        'data' => [
            ['id' => 1, 'name' => 'Main Dish', 'description' => 'Primary dishes'],
            ['id' => 2, 'name' => 'Appetizer', 'description' => 'Starters'],
            ['id' => 3, 'name' => 'Dessert', 'description' => 'Sweet treats'],
            ['id' => 4, 'name' => 'Beverages', 'description' => 'Drinks']
        ]
    ]);
});

Route::get('/ingredients', function () {
    return response()->json([
        'success' => true,
        'data' => [
            ['id' => 1, 'name' => 'Rice', 'category' => 'Grains'],
            ['id' => 2, 'name' => 'Chicken', 'category' => 'Meat'],
            ['id' => 3, 'name' => 'Pork', 'category' => 'Meat'],
            ['id' => 4, 'name' => 'Fish', 'category' => 'Seafood'],
            ['id' => 5, 'name' => 'Vegetables', 'category' => 'Vegetables']
        ]
    ]);
});

Route::middleware('auth:sanctum')->get('/analytics/daily-sales', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'total_sales' => 0,
            'orders_count' => 0,
            'average_order_value' => 0,
            'date' => now()->format('Y-m-d')
        ]
    ]);
});
