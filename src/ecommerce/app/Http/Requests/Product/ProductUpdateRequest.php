<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'article' => 'sometimes|string|unique:products,article|max:50',
            'description' => 'sometimes|string',
            'release_date' => 'sometimes|date',
            'price' => 'sometimes|decimal:2|min:0|max:99999999.99',
            'manufacturer_id' => 'sometimes|exists:manufacturers,id',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'maintenance_ids' => 'nullable|array',
            'maintenance_ids.*.id' => 'required_with:maintenance_ids|exists:maintenances,id',
            'maintenance_ids.*.price' => 'required_with:maintenance_ids|decimal:2|min:0|max:99999999.99',
        ];
    }
}
