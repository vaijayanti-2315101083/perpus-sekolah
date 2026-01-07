<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\BorrowApiController;
use App\Http\Controllers\Api\MemberApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\ReturnApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Perpustakaan
|--------------------------------------------------------------------------
|
| 🔓 PUBLIC: Dapat diakses tanpa autentikasi
| 🔒 AUTHENTICATED: Memerlukan Bearer Token (Sanctum)
| 🛡️ ADMIN ONLY: Memerlukan role Admin/Pustakawan
|
*/

// ============================================================================
// 🔓 PUBLIC ROUTES
// ============================================================================

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
});

// Public book listing
Route::get('/books', [BookApiController::class, 'index']);
Route::get('/books/{id}', [BookApiController::class, 'show']);

// ============================================================================
// 🔒 AUTHENTICATED ROUTES
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
    });

    // Dashboard & Reports (semua authenticated user)
    Route::get('/dashboard', [ReportApiController::class, 'dashboard']);
    Route::get('/reports', [ReportApiController::class, 'index']);
    Route::get('/reports/monthly', [ReportApiController::class, 'monthly']);
    Route::get('/reports/categories', [ReportApiController::class, 'categories']);
    Route::get('/reports/fines', [ReportApiController::class, 'fines']);

    // Borrows - Member dapat mengajukan peminjaman
    Route::get('/borrows', [BorrowApiController::class, 'index']);
    Route::get('/borrows/{id}', [BorrowApiController::class, 'show']);
    Route::post('/borrows', [BorrowApiController::class, 'store']);

    // Returns - Member dapat mengajukan pengembalian
    Route::get('/returns', [ReturnApiController::class, 'index']);
    Route::get('/returns/{id}', [ReturnApiController::class, 'show']);
    Route::post('/returns', [ReturnApiController::class, 'store']);

    // ============================================================================
    // 🛡️ ADMIN/PUSTAKAWAN ONLY ROUTES
    // ============================================================================
    
    Route::middleware('superuser')->group(function () {
        
        // Books Management
        Route::post('/books', [BookApiController::class, 'store']);
        Route::put('/books/{id}', [BookApiController::class, 'update']);
        Route::delete('/books/{id}', [BookApiController::class, 'destroy']);

        // Borrows Management
        Route::patch('/borrows/{id}/confirm', [BorrowApiController::class, 'confirm']);
        Route::delete('/borrows/{id}', [BorrowApiController::class, 'destroy']);

        // Returns Management
        Route::patch('/returns/{id}/process', [ReturnApiController::class, 'process']);
        Route::patch('/returns/{id}/pay', [ReturnApiController::class, 'pay']);
        Route::delete('/returns/{id}', [ReturnApiController::class, 'destroy']);

        // Members Management
        Route::apiResource('/members', MemberApiController::class);
    });
});
