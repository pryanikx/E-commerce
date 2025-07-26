<?php

declare(strict_types=1);

namespace app\DTO\Category;

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
}
