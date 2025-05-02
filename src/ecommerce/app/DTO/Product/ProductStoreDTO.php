<?php

namespace App\DTO\Product;

use App\Http\Requests\ProductStoreRequest;

readonly class ProductStoreDTO
{
    public string $name;
    public string $article;
    public string $description;
    public string $release_date;
    public float $price;
    public \Illuminate\Http\UploadedFile $image;
    public int $manufacturer_id;
    public int $category_id;
    public array $maintenances;

    public function __construct(ProductStoreRequest $request) {
        $this->name = $request->input('name');
        $this->article = $request->input('article');
        $this->description = $request->input('description');
        $this->release_date = $request->input('release_date');
        $this->price = $request->input('price');
        $this->image = $request->file('image');
        $this->manufacturer_id = $request->input('manufacturer_id');
        $this->category_id = $request->input('category_id');
        $this->maintenances = collect($request->input('maintenance_ids', []))
            ->mapWithKeys(fn($m) => [$m['id'] => ['price' => $m['price']]])
            ->toArray();
    }
}
