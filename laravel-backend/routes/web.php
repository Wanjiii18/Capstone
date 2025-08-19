<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\AuthController as WebAuthController;
use App\Http\Controllers\web\DashController;
use App\Http\Controllers\web\KarenderiaController;

Route::get('/', function () {
    return view('welcome');
});

// Show login and registration forms
Route::get('login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::get('register', [WebAuthController::class, 'showRegistrationForm'])->name('register');

// Handle login and registration submissions
Route::post('login', [WebAuthController::class, 'login'])->name('authenticate');
Route::post('register', [WebAuthController::class, 'register'])->name('store');

// Logout route
Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

// Dashboard route
Route::get('dashboard', [DashController::class, 'showMainDashboard'])->name('dashboard');
Route::get('dashboard/karenderia', [DashController::class, 'showKarenderiaDash'])->name('dashboard.karenderia');
Route::get('dashboard/meals', [DashController::class, 'showMealDashboard'])->name('dashboard.meals');
Route::get('dashboard/users', [DashController::class, 'showUserDashboard'])->name('dashboard.users');
Route::get('dashboard/reports', [DashController::class, 'showReportDashboard'])->name('dashboard.reports');

// Karenderia route
Route::get('karenderia/profile', [KarenderiaController::class, 'showKarenderiaProfile'])->name('karenderia.profile');



// Fallback route for undefined web routes
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

