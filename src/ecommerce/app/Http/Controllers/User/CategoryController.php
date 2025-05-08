<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {
    }

    /**
     * List all categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        if (empty($categories)) {
            return response()->json(['message' => __('messages.empty_categories')], 200);
        }

        return response()->json($categories, 200);
    }

    /**
     * List products of the specified category.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function products(int $id): JsonResponse
    {
        $products = $this->categoryService->getProductsForCategory($id);

        return response()->json($products);
    }
}
