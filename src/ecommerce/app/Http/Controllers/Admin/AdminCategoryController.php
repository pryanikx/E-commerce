<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\DeleteDataException;
use App\Http\Controllers\User\CategoryController;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use Illuminate\Http\JsonResponse;

class AdminCategoryController extends CategoryController
{
    /**
     * Store a new category.
     *
     * @param CategoryStoreRequest $request
     *
     * @return JsonResponse
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json($category, 201);
    }

    /**
     * Update an existing category.
     *
     * @param int $id
     * @param CategoryUpdateRequest $request
     *
     * @return JsonResponse
     */
    public function update(int $id, CategoryUpdateRequest $request): JsonResponse
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        return response()->json($category, 200);
    }

    /**
     * Erase an existing category.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws DeleteDataException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return response()->json(['message' => __('messages.deleted')], 200);
    }
}
