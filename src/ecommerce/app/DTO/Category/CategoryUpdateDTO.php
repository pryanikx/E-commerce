<?php

declare(strict_types=1);

namespace app\DTO\Category;

class CategoryUpdateDTO
{
    /**
     * @param int $id
     * @param string|null $name
     * @param string|null $alias
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $alias,
    ) {
    }
}
