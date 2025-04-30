<?php

namespace App\DTO\Product;

use App\Http\Requests\ProductUpdateRequest;

readonly class ProductUpdateDTO
{
    public string $description;
    public float $price;
    public \Illuminate\Http\UploadedFile $image;
    public array $services;


    public function __construct(ProductUpdateRequest $request) {
        $this->description = $request->input('description');
        $this->price = $request->input('price');
        $this->image = $request->file('image');
        $this->services = collect($request->input('service_ids', []))
            ->mapWithKeys(fn($s) => [$s['id'] => ['price' => $s['price']]])
            ->toArray();
    }
}
