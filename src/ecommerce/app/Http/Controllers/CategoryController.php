<?php

namespace App\Http\Controllers;

use App\DTO\Category\CategoryListDTO;
use App\DTO\Product\ProductListDTO;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found!'], 200);
        }

        $result = $categories->map(fn($category) => (new CategoryListDTO($category))->toArray());

        return response()->json($result);
    }

    public function products(int $id): JsonResponse
    {
        $category = $this->categoryService->find($id);

        $products = $category->products()
            ->paginate(10)
            ->through(fn($product) => (new ProductListDTO($product))->toArray());

        return response()->json($products);
    }
}
