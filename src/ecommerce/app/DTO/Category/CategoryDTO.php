<?php

declare(strict_types=1);

namespace App\DTO\Category;

class CategoryDTO
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
}
