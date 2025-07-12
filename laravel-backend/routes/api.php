<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KarenderiaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ApplicationController;

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
    Route::post('/upload-photo', [UserController::class, 'uploadPhoto']);
    Route::get('/nutritional-preferences', [UserController::class, 'getNutritionalPreferences']);
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
Route::middleware('auth:sanctum')->prefix('karenderias')->group(function () {
    Route::get('/', [KarenderiaController::class, 'index']);
    Route::post('/', [KarenderiaController::class, 'store']);
    Route::get('/nearby', [KarenderiaController::class, 'nearby']);
    Route::get('/search', [KarenderiaController::class, 'search']);
    Route::get('/{id}', [KarenderiaController::class, 'show']);
    Route::put('/{id}', [KarenderiaController::class, 'update']);
    Route::delete('/{id}', [KarenderiaController::class, 'destroy']);
});

// Karenderia application routes
Route::middleware('auth:sanctum')->prefix('karenderia/applications')->group(function () {
    Route::get('/', [ApplicationController::class, 'index']);
    Route::post('/', [ApplicationController::class, 'store']);
    Route::get('/applicant/{applicantId}', [ApplicationController::class, 'getByApplicant']);
    Route::put('/{id}/approve', [ApplicationController::class, 'approve']);
    Route::put('/{id}/reject', [ApplicationController::class, 'reject']);
});

// Menu routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/menu-items', [MenuController::class, 'getMenuItems']);
    Route::post('/menu-items', [MenuController::class, 'createMenuItem']);
    Route::put('/menu-items/{id}', [MenuController::class, 'updateMenuItem']);
    Route::delete('/menu-items/{id}', [MenuController::class, 'deleteMenuItem']);
    
    Route::get('/ingredients', [MenuController::class, 'getIngredients']);
    Route::post('/ingredients', [MenuController::class, 'createIngredient']);
    Route::put('/ingredients/{id}', [MenuController::class, 'updateIngredient']);
    Route::delete('/ingredients/{id}', [MenuController::class, 'deleteIngredient']);
    
    Route::get('/menu-categories', [MenuController::class, 'getCategories']);
    Route::post('/menu-categories', [MenuController::class, 'createCategory']);
});

// Order routes
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/recent', [OrderController::class, 'getRecentOrders']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
});

// Analytics routes
Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('/daily-sales', [AnalyticsController::class, 'getDailySales']);
    Route::get('/sales/{karenderiaId}', [AnalyticsController::class, 'getSalesAnalytics']);
    Route::get('/popular-items/season', [AnalyticsController::class, 'getPopularItemsBySeason']);
});

// Public routes (no auth required)
Route::get('/public/karenderias', [KarenderiaController::class, 'publicIndex']);
Route::get('/public/karenderias/{id}', [KarenderiaController::class, 'publicShow']);
