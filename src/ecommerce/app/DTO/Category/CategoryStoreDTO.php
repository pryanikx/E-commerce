<?php

declare(strict_types=1);

namespace App\DTO\Category;

/**
 * Data transfer object for storing a new category.
 */
readonly class CategoryStoreDTO
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $alias
     */
    public string $alias;

    /**
     * @param array $request_data
     */
    public function __construct(array $request_data)
    {
        $this->name = $request_data['name'];
        $this->alias = $request_data['alias'];
    }
}
