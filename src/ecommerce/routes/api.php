<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminMaintenanceController;
use App\Http\Controllers\Admin\AdminManufacturerController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\ProductExportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:user'])->name('user.')->group(function () {
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);

    Route::apiResource('categories', CategoryController::class)->only(['index']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('amdin.')->group(function () {
    Route::get('/export-catalog', [ProductExportController::class, 'export']);

    Route::apiResource('products', AdminProductController::class);

    Route::apiResource('manufacturers', AdminManufacturerController::class)->except(['show']);

    Route::apiResource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('/categories/{id}/products', [AdminCategoryController::class, 'products']);

    Route::apiResource('maintenance', AdminMaintenanceController::class)->except(['show']);
});

Route::fallback(function () {
    return response()->json([
        'error' => __('errors.route_not_found')
    ], 404);
});
