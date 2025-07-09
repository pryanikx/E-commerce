<?php

declare(strict_types=1);

namespace App\DTO\Category;

class CategoryDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $alias,
        public string $created_at,
        public string $updated_at,
    ) {}
} 