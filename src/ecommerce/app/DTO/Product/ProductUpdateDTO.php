<?php

namespace App\DTO\Product;

use App\Http\Requests\ProductUpdateRequest;

readonly class ProductUpdateDTO
{
    public string $description;
    public float $price;
    public \Illuminate\Http\UploadedFile $image;
    public array $maintenances;


    public function __construct(ProductUpdateRequest $request) {
        $this->description = $request->input('description');
        $this->price = $request->input('price');
        $this->image = $request->file('image');
        $this->maintenances = collect($request->input('maintenance_ids', []))
            ->mapWithKeys(fn($m) => [$m['id'] => ['price' => $m['price']]])
            ->toArray();
    }
}
