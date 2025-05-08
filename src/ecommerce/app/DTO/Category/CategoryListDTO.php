<?php

declare(strict_types=1);

namespace App\DTO\Category;

use App\Models\Category;

readonly class CategoryListDTO
{
    public int $id;
    public string $name;
    public string $alias;

    public function __construct(Category $category)
    {
        $this->id = $category->id;
        $this->name = $category->name;
        $this->alias = $category->alias;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
        ];
    }
}
