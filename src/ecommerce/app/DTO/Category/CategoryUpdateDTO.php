<?php

declare(strict_types=1);

namespace App\DTO\Category;

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

    /**
     * Transform a DTO object to array.
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
