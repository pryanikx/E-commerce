<?php

declare(strict_types=1);

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductFilter
{
    /**
     * Apply filters to the product query.
     *
     * @param Builder|HasMany $query
     * @param array<string, mixed> $filters
     *
     * @return Builder|HasMany
     */
    public function applyFilters(Builder|HasMany $query, array $filters): Builder|HasMany
    {
        foreach ($filters as $filter => $value) {
            if ($this->isEmpty($value)) {
                continue;
            }

            $this->applySingleFilter($query, $filter, $value);
        }

        return $query;
    }

    /**
     * Apply a single filter to the query.
     *
     * @param Builder|HasMany $query
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

    /**
     * Check if a value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
