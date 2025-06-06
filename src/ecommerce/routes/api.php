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

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);

    Route::apiResource('categories', CategoryController::class)->only(['index']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/export-catalog', [ProductExportController::class, 'export']);

    Route::apiResource('products', AdminProductController::class)
        ->names([
            'index' => 'admin.products.index',
            'store' => 'admin.products.store',
            'show' => 'admin.products.show',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ]);

    Route::apiResource('manufacturers', AdminManufacturerController::class)->except(['show']);

    Route::apiResource('categories', AdminCategoryController::class)->except(['show'])
        ->names([
            'index' => 'admin.categories.index',
            'store' => 'admin.categories.store',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ]);

    Route::get('/categories/{id}/products', [AdminCategoryController::class, 'products']);

    Route::apiResource('maintenance', AdminMaintenanceController::class)->except(['show']);
});

Route::fallback(function () {
    return response()->json([
        'error' => __('errors.route_not_found')
    ], 404);
});
