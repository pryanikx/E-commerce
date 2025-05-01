<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ManufacturerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::delete('/products/{id}', [AdminProductController::class, 'deleteProduct']);
    Route::patch('/products/{id}', [AdminProductController::class, 'updateProduct']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::delete('/services/{id}', [ServiceController::class, 'deleteService']);
    Route::patch('/services/{id}', [ServiceController::class, 'updateService']);

    Route::get('/manufacturers', [ManufacturerController::class, 'index']);
    //Route::get('/manufacturers/{id}', [ManufacturerController::class, 'show']);
    Route::post('/manufacturers', [ManufacturerController::class, 'store']);
    Route::delete('/manufacturers/{id}', [ManufacturerController::class, 'deleteManufacturer']);
    Route::patch('/manufacturers/{id}', [ManufacturerController::class, 'updateManufacturer']);
});

