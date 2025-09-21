<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuItemController;

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
    Route::get('/nearby', [KarenderiaController::class, 'nearby']);
    
    // Protected routes for karenderia owners (must come before {id} route)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [KarenderiaController::class, 'store']);
        Route::get('/my-karenderia', [KarenderiaController::class, 'myKarenderia']);
        Route::put('/{id}', [KarenderiaController::class, 'update']);
        Route::put('/{id}/data', [KarenderiaController::class, 'updateKarenderiaData']);
        Route::delete('/{id}', [KarenderiaController::class, 'destroy']);
    });
    
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

// Menu Items routes
Route::middleware('auth:sanctum')->prefix('menu-items')->group(function () {
    Route::post('/', [MenuItemController::class, 'store']);
    Route::get('/', [MenuItemController::class, 'index']);
    Route::get('/my-menu', [MenuItemController::class, 'getMyMenuItems']); // Added missing route
    Route::get('/{id}', [MenuItemController::class, 'show']);
    Route::put('/{id}', [MenuItemController::class, 'update']);
    Route::delete('/{id}', [MenuItemController::class, 'destroy']);
});

// Menu Categories routes
Route::middleware('auth:sanctum')->prefix('menu-categories')->group(function () {
    Route::get('/', [MenuItemController::class, 'getCategories']);
    Route::post('/', [MenuItemController::class, 'createCategory']);
    Route::put('/{id}', [MenuItemController::class, 'updateCategory']);
    Route::delete('/{id}', [MenuItemController::class, 'deleteCategory']);
});

// Ingredients routes
Route::middleware('auth:sanctum')->prefix('ingredients')->group(function () {
    Route::get('/', [MenuItemController::class, 'getIngredients']);
    Route::post('/', [MenuItemController::class, 'createIngredient']);
    Route::put('/{id}', [MenuItemController::class, 'updateIngredient']);
    Route::delete('/{id}', [MenuItemController::class, 'deleteIngredient']);
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
