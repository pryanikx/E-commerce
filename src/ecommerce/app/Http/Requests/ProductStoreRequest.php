<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'article' => 'required|string',
            'description' => 'required|string',
            'release_date' => 'required|date',
            'price' => 'required|decimal:2|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'category_id' => 'required|exists:categories,id',
            'service_ids' => 'nullable|array',
            'service_ids.*.id' => 'required|exists:services,id',
            'service_ids.*.price' => 'required|decimal:2|min:0',
        ];
    }
}
