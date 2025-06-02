<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'name' => 'sometimes|string|max:255',
            'article' => ['sometimes', 'string', 'max:50', 'unique:products,article,' . $productId],
            'description' => 'nullable|string',
            'release_date' => 'sometimes|date',
            'price' => 'sometimes|numeric|min:0|max:99999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'manufacturer_id' => 'sometimes|exists:manufacturers,id',
            'category_id' => 'sometimes|exists:categories,id',
            'maintenance_ids' => 'nullable|array',
            'maintenance_ids.*.id' => 'required_with:maintenance_ids|exists:maintenances,id',
            'maintenance_ids.*.price' => 'required_with:maintenance_ids|numeric|min:0|max:99999999.99',
        ];
    }
}



