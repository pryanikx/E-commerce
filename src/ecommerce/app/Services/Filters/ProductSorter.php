<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductSorter
{
    private const DEFAULT_SORT_COLUMN = 'id';

    private const SORT_COLUMNS = ['price', 'release_date', 'id'];

    private const DEFAULT_SORT_ORDER = 'asc';

    private const SORT_ORDERS = ['asc', 'desc'];

    /**
     * Apply sorting to the product query.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @param Builder<Product>|HasMany<Product, TModel> $query
     * @param array<string, string> $sorters
     *
     * @return Builder<Product>|HasMany<Product, TModel>
     */
    public function applySorters(Builder|HasMany $query, array $sorters): Builder|HasMany
    {
        $sortBy = $this->resolveSortColumn($sorters['sort_by'] ?? null);
        $sortOrder = $this->resolveSortOrder($sorters['sort_order'] ?? null);

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Resolve the sort column.
     *
     * @param string|null $column
     *
     * @return string
     */
    private function resolveSortColumn(?string $column): string
    {
        return in_array($column, self::SORT_COLUMNS, true)
            ? $column
            : self::DEFAULT_SORT_COLUMN;
    }

    /**
     * Resolve the sort order.
     *
     * @param string|null $order
     *
     * @return string
     */
    private function resolveSortOrder(?string $order): string
    {
        return in_array($order, self::SORT_ORDERS, true)
            ? $order
            : self::DEFAULT_SORT_ORDER;
    }
}
