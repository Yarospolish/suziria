<?php

namespace App\DTO;

use App\Enums\ProductCategory;
use App\Utils\Validator;

class UpdateProductDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?float $price = null,
        public readonly ?ProductCategory $category = null,
        public readonly ?array $attributes = null
    ) {}

    public static function fromRequest(array $data): self
    {
        Validator::validate($data, [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|in:electronics,clothing,food,furniture,other',
            'attributes' => 'sometimes|array'
        ]);

        return new self(
            $data['name'] ?? null,
            isset($data['price']) ? (float)$data['price'] : null,
            isset($data['category']) ? ProductCategory::from($data['category']) : null,
            $data['attributes'] ?? null
        );
    }

    public function hasUpdates(): bool
    {
        return $this->name !== null
            || $this->price !== null
            || $this->category !== null
            || $this->attributes !== null;
    }
}