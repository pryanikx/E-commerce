<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryProductRequest extends FormRequest
{
    public const SORT_COLUMNS = ['id', 'price', 'release_date'];

    public const SORT_ORDERS = ['asc', 'desc'];

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'manufacturer_id' => 'nullable|integer|exists:manufacturers,id',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0|gte:price_min',
            'sort_by' => ['nullable', Rule::in(self::SORT_COLUMNS)],
            'sort_order' => ['nullable', Rule::in(self::SORT_ORDERS)],
            'page' => 'nullable|integer|min:1',
        ];
    }
}
