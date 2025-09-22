<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\web\AuthController as WebAuthController;
use App\Http\Controllers\web\DashController;
use App\Http\Controllers\web\KarenderiaController;
use App\Http\Controllers\web\MenuItemController;
use App\Http\Controllers\web\UserController;
use App\Http\Controllers\web\PendingController;
use App\Http\Controllers\web\ReportController;

Route::get('/', [WebAuthController::class, 'showLoginForm'])->name('login');

// authentication routes
Route::get('login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [WebAuthController::class, 'login'])->name('authenticate');
Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::get('dashboard', [DashController::class, 'showMainDashboard'])->name('dashboard');
    Route::get('dashboard/karenderia', [KarenderiaController::class, 'showKarenderiaDashboard'])->name('dashboard.karenderia');
    Route::get('dashboard/menu', [MenuItemController::class, 'showMenuDashboard'])->name('dashboard.menu');
    Route::get('dashboard/meals', [MenuItemController::class, 'showMenuDashboard'])->name('dashboard.meals');
    Route::get('dashboard/users', [UserController::class, 'showUserDashboard'])->name('dashboard.users');
    Route::get('dashboard/pending', [PendingController::class, 'showPendingDashboard'])->name('dashboard.pending');

    // Details Route
    Route::get('karenderias/{id}/profile', [KarenderiaController::class, 'showKarenderiaProfile'])->name('dashboardProfile.karenderiaProfile');
    Route::get('menus/{id}/profile', [MenuItemController::class, 'show'])->name('dashboardProfile.menuItemProfile');
    Route::get('users/{id}/profile', [UserController::class, 'show'])->name('dashboardProfile.userProfile');

    // CRUD operations for Karenderia
    Route::get('karenderias/create', [KarenderiaController::class, 'create'])->name('karenderia.create');
    Route::post('karenderias', [KarenderiaController::class, 'store'])->name('karenderia.store');
    Route::get('karenderias/{id}/edit', [KarenderiaController::class, 'edit'])->name('karenderia.edit');
    Route::put('karenderias/{id}', [KarenderiaController::class, 'update'])->name('karenderia.update');
    Route::post('karenderias/{id}/approve', [KarenderiaController::class, 'approve'])->name('karenderia.approve');
    Route::delete('karenderias/{id}', [KarenderiaController::class, 'destroy'])->name('karenderia.destroy');

    // CRUD operations for Reports
    Route::resource('dashboard/reports', ReportController::class);

    // Karenderia Dashboard route
    Route::get('/karenderia/dashboard', [KarenderiaController::class, 'dashboard'])->name('karenderia.karenderiaDash');
});

// Fallback route for undefined web routes
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

