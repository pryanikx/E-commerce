<?php

namespace App\DTO\Category;

readonly class CategoryUpdateDTO
{
    public ?string $name;
    public ?string $alias;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'] ?? null;
        $this->alias = $request_data['alias'] ?? null;
    }
}
