<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'article' => ['sometimes', 'string', 'max:100', 'unique:products,article,' . $productId],
            'description' => ['sometimes', 'string', 'nullable'],
            'release_date' => ['sometimes', 'date'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'manufacturer_id' => ['sometimes', 'integer', 'exists:manufacturers,id'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'maintenance_ids' => ['sometimes', 'array'],
            'maintenance_ids.*.id' => ['required_with:maintenance_ids', 'integer', 'exists:maintenances,id'],
            'maintenance_ids.*.price' => ['required_with:maintenance_ids', 'numeric', 'min:0'],
        ];
    }
}
