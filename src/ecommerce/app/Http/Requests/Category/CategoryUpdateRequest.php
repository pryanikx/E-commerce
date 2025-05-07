<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'alias' => 'sometimes|string|max:50|regex:/^[a-z0-9-]+$/|unique:categories,alias',
        ];
    }
}
