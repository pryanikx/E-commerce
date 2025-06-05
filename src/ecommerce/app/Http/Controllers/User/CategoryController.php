<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryProductRequest;
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
     * @param CategoryProductRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function products(CategoryProductRequest $request, int $id): JsonResponse
    {
        $filters = [
            'manufacturer_id' => $request->validated('manufacturer_id'),
            'price_min' => $request->validated('price_min'),
            'price_max' => $request->validated('price_max'),
        ];

        $sort = [
            'sort_by' => $request->validated('sort_by', 'id'),
            'sort_order' => $request->validated('sort_order', 'asc'),
        ];

        $products = $this->categoryService->getProductsForCategory($id, $filters, $sort);

        return response()->json($products);
    }
}
