<?php

namespace App\DTO;

use App\Enums\ProductCategory;
use App\Utils\Validator;

class ListProductsDTO
{
    public function __construct(
        public readonly ?ProductCategory $category,
        public readonly ?float $minPrice,
        public readonly ?float $maxPrice
    ) {}

    public static function fromQueryParams(array $queryParams): self
    {
        Validator::validate($queryParams, [
            'category' => 'sometimes|in:electronics,clothing,food,furniture,other',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0'
        ]);

        return new self(
            isset($queryParams['category']) ? ProductCategory::from($queryParams['category']) : null,
            isset($queryParams['min_price']) ? (float)$queryParams['min_price'] : null,
            isset($queryParams['max_price']) ? (float)$queryParams['max_price'] : null
        );
    }
}