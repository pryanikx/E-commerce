<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseProductRequest extends FormRequest
{
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
}
