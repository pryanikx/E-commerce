<?php

declare(strict_types=1);

namespace App\DTO\Category;

use App\Models\Category;

/**
 * Data transfer object for listing categories.
 */
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
    ) {}

    /**
     * @return array<string, int|string>
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
