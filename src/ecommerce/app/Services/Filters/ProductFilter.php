<?php

declare(strict_types=1);

namespace App\Services\Filters;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductFilter
{
    /**
     * Apply filters to the product query.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @param Builder<Product>|HasMany<Product, TModel> $query
     * @param array<string, mixed> $filters
     *
     * @return Builder<Product>|HasMany<Product, TModel>
     */
    public function applyFilters(Builder|HasMany $query, array $filters): Builder|HasMany
    {
        foreach ($filters as $filter => $value) {
            if (empty($value)) {
                continue;
            }

            $this->applySingleFilter($query, $filter, $value);
        }

        return $query;
    }

    /**
     * Apply a single filter to the query.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @param Builder<Product>|HasMany<Product, TModel> $query
     * @param string $filter
     * @param mixed $value
     *
     * @return void
     */
    private function applySingleFilter(Builder|HasMany $query, string $filter, mixed $value): void
    {
        match ($filter) {
            'manufacturer_id' => $query->where('manufacturer_id', (int) $value),
            'price_min' => $query->where('price', '>=', (float) $value),
            'price_max' => $query->where('price', '<=', (float) $value),
            default => null,
        };
    }
}
