<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class CategoryUpdateDTO
{
    /**
     * @var string|null $name
     */
    public ?string $name;

    /**
     * @var string|null $alias
     */
    public ?string $alias;

    /**
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'] ?? null;
        $this->alias = $request_data['alias'] ?? null;
    }
}
