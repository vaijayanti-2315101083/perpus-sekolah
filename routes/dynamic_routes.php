<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LibrarianController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\RestoreController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin & Pustakawan Routes (Dynamic Prefix)
|--------------------------------------------------------------------------
|
| Routes ini support 2 prefix:
| - /admin/... untuk Admin
| - /pustakawan/... untuk Pustakawan
|
| Middleware: auth + superuser (Admin atau Pustakawan)
|
*/

// Helper function to register routes for both prefixes
$registerAdminRoutes = function ($prefix) {
    Route::prefix($prefix)->name("{$prefix}.")->middleware(['auth', 'superuser'])->group(function () use ($prefix) {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile untuk Admin/Pustakawan
        Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
            Route::put('/password', 'updatePassword')->name('password.update');
            Route::put('/photo', 'updatePhoto')->name('photo.update');
            Route::delete('/photo', 'deletePhoto')->name('photo.delete');
        });

        // Librarians (Admin Only)
        if ($prefix === 'admin') {
            Route::middleware('admin')->group(function () {
                Route::resource('/librarians', LibrarianController::class)->except('show');
            });
        }

        // Members
        Route::resource('/members', MemberController::class)->except('show');

        // Books
        Route::resource('/books', BookController::class)->except('show');

        // Borrows
        Route::resource('/borrows', BorrowController::class)->only(['index', 'edit', 'update', 'destroy']);

        // Returns (Restores)
        Route::resource('/returns', RestoreController::class)
            ->only(['index', 'edit', 'update', 'destroy']);

        // 🔥 PEMBAYARAN DENDA (ADMIN & PUSTAKAWAN CUMA LIHAT STATUS)
        Route::patch('/returns/{id}/pay', [RestoreController::class, 'markAsPaid'])
            ->name('returns.pay');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])
            ->name('activity-logs.index');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('reports.index');
        Route::get('/reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])
            ->name('reports.export-pdf');
    
    });
};

// Register routes for both prefixes
$registerAdminRoutes('admin');
$registerAdminRoutes('pustakawan');