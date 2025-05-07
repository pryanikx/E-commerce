<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\AdminManufacturerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\User\ProductController;
use Illuminate\Support\Facades\Route;


// TODO: add fallbacks

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


/*Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [AdminProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [AdminProductController::class, 'update']);
    Route::delete('/{id}', [AdminProductController::class, 'destroy']);
});

Route::prefix('maintenances')->group(function () {
    Route::get('/', [AdminMaintenanceController::class, 'index']);
    Route::post('/', [AdminMaintenanceController::class, 'store']);
    Route::put('/{id}', [AdminMaintenanceController::class, 'update']);
    Route::delete('/{id}', [AdminMaintenanceController::class, 'destroy']);
});

Route::prefix('manufacturers')->group(function () {
    Route::get('/', [AdminManufacturerController::class, 'index']);
    Route::post('/', [AdminManufacturerController::class, 'store']);
    Route::put('/{id}', [AdminManufacturerController::class, 'update']);
    Route::delete('/{id}', [AdminManufacturerController::class, 'destroy']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [AdminCategoryController::class, 'index']);
    Route::post('/', [AdminCategoryController::class, 'store']);
    Route::put('/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);
    Route::get('/{id}/products', [AdminCategoryController::class, 'products']);
});*/


// TODO: resource controllers & routes
// TODO: add fallbacks

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

    Route::prefix('products')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::get('/{id}/products', [AdminCategoryController::class, 'products']);
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
        Route::get('/', [AdminMaintenanceController::class, 'index']);
        Route::post('/', [AdminMaintenanceController::class, 'store']);
        Route::delete('/{id}', [AdminMaintenanceController::class, 'destroy']);
        Route::patch('/{id}', [AdminMaintenanceController::class, 'update']);
    });

    Route::prefix('manufacturers')->group(function () {
        Route::get('/', [AdminManufacturerController::class, 'index']);
        Route::post('/', [AdminManufacturerController::class, 'store']);
        Route::delete('/{id}', [AdminManufacturerController::class, 'destroy']);
        Route::patch('/{id}', [AdminManufacturerController::class, 'update']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index']);
        Route::post('/', [AdminCategoryController::class, 'store']);
        Route::put('/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);
        Route::get('/{id}/products', [AdminCategoryController::class, 'products']);
    });

});

