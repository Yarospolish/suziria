<?php

namespace App\DTO;

use App\Enums\ProductCategory;
use App\Utils\Validator;

class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly ProductCategory $category,
        public readonly array $attributes
    ) {}

    public static function fromRequest(array $data): self
    {
        Validator::validate($data, [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:electronics,clothing,food,furniture,other',
            'attributes' => 'required|array'
        ]);

        return new self(
            $data['name'],
            (float)$data['price'],
            ProductCategory::from($data['category']),
            $data['attributes']
        );
    }
}