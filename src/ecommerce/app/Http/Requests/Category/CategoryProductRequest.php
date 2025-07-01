<?php

declare(strict_types=1);

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryProductRequest extends FormRequest
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
        return [
            'page' => ['sometimes', 'integer', 'min:1'],

            'manufacturer_id' => ['sometimes', 'integer', 'exists:manufacturers,id'],
            'price_min' => ['sometimes', 'numeric', 'min:0'],
            'price_max' => ['sometimes', 'numeric', 'min:0', 'gte:price_min'],

            'sort_by' => ['sometimes', 'string', 'in:price,release_date,id'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Get the filters from the validated data.
     *
     * @return array<string, mixed>
     */
    public function getFilters(): array
    {
        return [
            'manufacturer_id' => $this->validated('manufacturer_id'),
            'price_min' => $this->validated('price_min'),
            'price_max' => $this->validated('price_max'),
        ];
    }

    /**
     * Get the sorting parameters from the validated data.
     *
     * @return array<string, string>
     */
    public function getSortParams(): array
    {
        return [
            'sort_by' => $this->validated('sort_by', 'id'),
            'sort_order' => $this->validated('sort_order', 'asc'),
        ];
    }

    /**
     * Get the page number from the validated data.
     *
     * @return int
     */
    public function getPage(): int
    {
        return (int) $this->validated('page', 1);
    }
}
