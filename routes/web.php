<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBookController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Fixed (No Inline Middleware)
|--------------------------------------------------------------------------
|
| File ini berisi:
|   ✅ Public routes (home, search, preview)
|   ✅ Guest routes (login, register)
|   ✅ Member routes (my-books, profile, logout)
|
| Admin/Pustakawan routes ada di: routes/dynamic_routes.php
|
*/

// ============================================================================
// PUBLIC ROUTES (No Authentication)
// ============================================================================

Route::get('/', HomeController::class)->name('home');
Route::get('/search', SearchController::class)->name('search');
Route::get('/preview/{book}', PreviewController::class)->name('preview');

// ============================================================================
// GUEST ROUTES (Login & Register)
// ============================================================================

Route::middleware('guest')->group(function () {
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'store']);

    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

// ============================================================================
// AUTHENTICATED USER ROUTES
// ============================================================================

Route::middleware('auth')->group(function () {
    // Logout (Semua Role)
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    // My Books (Member Only)
    Route::resource('/my-books', MyBookController::class)->only('index', 'update');
    Route::post('/my-books/{book}', [MyBookController::class, 'store'])->name('my-books.store');

    // Profile Management (Member Only)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
    });
});

// ============================================================================
// ADMIN & PUSTAKAWAN ROUTES (Dynamic Prefix)
// ============================================================================
// 
// Admin: /admin/...
// Pustakawan: /pustakawan/...
//
// Routes termasuk profile untuk Admin & Pustakawan
//
// ============================================================================

require __DIR__.'/dynamic_routes.php';