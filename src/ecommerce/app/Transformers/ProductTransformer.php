<?php

declare(strict_types=1);

namespace App\Transformers;

use App\DTO\Product\ProductDTO;
use App\Services\Currency\CurrencyCalculatorService;
use App\Services\Support\ImageService;

class ProductTransformer
{
    /**
     * @param CurrencyCalculatorService $currencyCalculator
     * @param ImageService $imageService
     */
    public function __construct(
        private CurrencyCalculatorService $currencyCalculator,
        private ImageService $imageService
    ) {
    }

    /**
     * Transform a collection of products.
     *
     * @param ProductDTO[] $products
     * @return ProductDTO[]
     */
    public function transformCollection(array $products): array
    {
        return array_map([$this, 'transform'], $products);
    }

    /**
     * Transform a single product.
     *
     * @param ProductDTO $product
     * @return ProductDTO
     */
    public function transform(ProductDTO $product): ProductDTO
    {
        $product->price = $this->currencyCalculator->convert((float) $product->price);

        if ($product->maintenances) {
            $product->maintenances = $this->transformMaintenances($product->maintenances);
        }

        $product->imagePath = $this->imageService->getImageUrlOrNull($product->imagePath);

        return $product;
    }

    /**
     * Transform maintenances prices.
     *
     * @param array<int, mixed> $maintenances
     * @return array<int, mixed>
     */
    private function transformMaintenances(array $maintenances): array
    {
        return array_map(function ($maintenance) {
            $maintenance['price'] = $this->currencyCalculator->convert((float) $maintenance['price']);

            return $maintenance;
        }, $maintenances);
    }

    /**
     * Process maintenance IDs with proper typing for save operations.
     *
     * @param mixed $maintenanceIds
     * @return array<int, mixed>
     */
    public function processMaintenancesForSave(mixed $maintenanceIds): array
    {
        if (!is_array($maintenanceIds) || empty($maintenanceIds)) {
            return [];
        }

        return collect($maintenanceIds)
            ->filter(
                fn ($maintenance) =>
                is_array($maintenance) &&
                isset($maintenance['id'], $maintenance['price'])
            )
            ->mapWithKeys(fn ($maintenance) => [
                (int) $maintenance['id'] => ['price' => (float) $maintenance['price']]
            ])
            ->all();
    }
}
