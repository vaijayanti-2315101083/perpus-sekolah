<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;

Route::get('/books', [BookApiController::class, 'index']);
Route::post('/books', [BookApiController::class, 'store']);
Route::delete('/books/{id}', [BookApiController::class, 'destroy']);
