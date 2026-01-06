<?php

use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
|
| Auto-generated profile routes for Admin, Pustakawan, and Member
|
*/

// Admin & Pustakawan Profile Routes
Route::middleware(['auth', 'superuser'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
    });
});

// Member Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
        Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
        Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
    });
});