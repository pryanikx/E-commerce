<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

class ProductStoreRequest extends BaseProductRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'article' => 'required|string|unique:products,article|max:50',
            'description' => 'required|string',
            'release_date' => 'required|date',
            'price' => 'required|decimal:2|min:0|max:99999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'category_id' => 'required|exists:categories,id',
            'maintenance_ids' => 'nullable|array',
            'maintenance_ids.*.id' => 'required|exists:maintenances,id',
            'maintenance_ids.*.price' => 'required|decimal:2|min:0|max:99999999.99',
        ];
    }
}
