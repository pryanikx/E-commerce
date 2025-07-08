<?php

declare(strict_types=1);

namespace App\DTO\Category;

/**
 * Data transfer object for updating a category.
 */
readonly class CategoryUpdateDTO
{
    /**
     * @param string|null $name
     * @param string|null $alias
     */
    public function __construct(
        public ?string $name = null,
        public ?string $alias = null,
    ) {}
}
