<?php

declare(strict_types=1);

namespace App\DTO\Category;

class CategoryStoreDTO
{
    /**
     * @param string $name
     * @param string $alias
     */
    public function __construct(
        public string $name,
        public string $alias,
    ) {
    }

    /**
     * Transform a DTO object to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'alias' => $this->alias,
        ];
    }
}
