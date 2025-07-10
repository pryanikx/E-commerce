<?php

declare(strict_types=1);

namespace App\DTO\Category;


readonly class CategoryUpdateDTO
{
    public ?string $name;
    public ?string $alias;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->alias = $data['alias'] ?? null;
    }
}
