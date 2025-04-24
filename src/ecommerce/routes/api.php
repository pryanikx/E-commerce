<?php

use app\Http\Controllers\ProductController;
use app\Http\Controllers\AdminProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Add 'update product' route
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::delete('/products/{id}', [AdminProductController::class, 'deleteProduct']);
    
    // Add 'update product' route
});
