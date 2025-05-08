<?php

declare(strict_types=1);

namespace App\Http\Requests\Maintenance;

use Illuminate\Foundation\Http\FormRequest;

class MaintenanceStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:maintenances,name',
            'description' => 'nullable|string',
            'duration' => 'nullable|string|max:50', // TODO: change duration to FROM & TILL
        ];
    }
}
