<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class CategoryStoreDTO
{
    public string $name;
    public string $alias;

    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->alias = $request_data['alias'];
    }
}
