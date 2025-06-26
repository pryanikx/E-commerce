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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('maintenance_ids') && is_array($this->maintenance_ids)) {
            $maintenanceIds = [];
            foreach ($this->maintenance_ids as $maintenance) {
                if (is_array($maintenance) && isset($maintenance['id'], $maintenance['price'])) {
                    $maintenanceIds[] = [
                        'id' => (int) $maintenance['id'],
                        'price' => (float) $maintenance['price']
                    ];
                }
            }
            $this->merge(['maintenance_ids' => $maintenanceIds]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'article' => 'sometimes|string|max:50|unique:products,article',
            'description' => 'sometimes|string',
            'release_date' => 'sometimes|date',
            'price' => 'sometimes|decimal:2|min:0|max:99999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'manufacturer_id' => 'sometimes|exists:manufacturers,id',
            'category_id' => 'sometimes|exists:categories,id',
            'maintenance_ids' => 'nullable|array',
            'maintenance_ids.*.id' => 'required|exists:maintenances,id',
            'maintenance_ids.*.price' => 'required|decimal:2|min:0|max:99999999.99',
        ];
    }
}
