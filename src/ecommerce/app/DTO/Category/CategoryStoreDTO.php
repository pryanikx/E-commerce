<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class CategoryStoreDTO
{
    public string $name;
    public string $alias;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->alias = $data['alias'];
    }
}
