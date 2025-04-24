<?php

namespace app\DTO;

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
    public array $services;


    public function __construct(ProductStoreRequest $request) {
        $this->name = $request->input('name');
        $this->article = $request->input('article');
        $this->description = $request->input('description');
        $this->release_date = $request->input('release_date');
        $this->price = $request->input('price');
        $this->image = $request->file('image_path');
        $this->manufacturer_id = $request->input('manufacturer_id');
        $this->category_id = $request->input('category_id');
        $this->services = collect($request->input('service_ids', []))
            ->mapWithKeys(fn($s) => [$s['id'] => ['price' => $s['price']]])
            ->toArray();
    }
}
