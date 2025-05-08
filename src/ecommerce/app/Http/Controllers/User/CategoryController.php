<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        if (empty($categories)) {
            return response()->json(['message' => 'No categories found!'], 200);
        }

        return response()->json($categories, 200);
    }

    public function products(int $id): JsonResponse
    {
        $products = $this->categoryService->getProductsForCategory($id);

        return response()->json($products);
    }
}
