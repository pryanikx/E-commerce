<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class CategoryListDTO
{
    /**
     * @param int $id
     * @param string $name
     * @param string $alias
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $alias,
    ) {
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
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
