<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ManufacturerController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

    Route::prefix('products')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/{id}/products', [CategoryController::class, 'products']);
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index']);
        Route::post('/', [AdminProductController::class, 'store']);
        Route::get('/{id}', [AdminProductController::class, 'show']);
        Route::delete('/{id}', [AdminProductController::class, 'destroy']);
        Route::patch('/{id}', [AdminProductController::class, 'update']);
    });

    Route::prefix('maintenances')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index']);
        Route::post('/', [MaintenanceController::class, 'store']);
        Route::delete('/{id}', [MaintenanceController::class, 'destroy']);
        Route::patch('/{id}', [MaintenanceController::class, 'update']);
    });

    Route::prefix('manufacturers')->group(function () {
        Route::get('/', [ManufacturerController::class, 'index']);
        Route::post('/', [ManufacturerController::class, 'store']);
        Route::delete('/{id}', [ManufacturerController::class, 'destroy']);
        Route::patch('/{id}', [ManufacturerController::class, 'update']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index']);
        Route::post('/', [AdminCategoryController::class, 'store']);
        Route::put('/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);
        Route::get('/{id}/products', [AdminCategoryController::class, 'products']);
    });

});

