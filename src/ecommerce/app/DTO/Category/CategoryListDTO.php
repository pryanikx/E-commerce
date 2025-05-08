<?php

declare(strict_types=1);

namespace App\DTO\Category;

use App\Models\Category;

readonly class CategoryListDTO
{
    /**
     * @var int $id
     */
    public int $id;

    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $alias
     */
    public string $alias;

    /**
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->id = $category->id;
        $this->name = $category->name;
        $this->alias = $category->alias;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
        ];
    }
}
