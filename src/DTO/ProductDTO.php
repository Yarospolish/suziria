<?php

namespace App\DTO;

use App\Enums\ProductCategory;
use Ramsey\Uuid\UuidInterface;
use DateTimeImmutable;

class ProductDTO
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $name,
        public readonly float $price,
        public readonly ProductCategory $category,
        public readonly array $attributes,
        public readonly DateTimeImmutable $createdAt
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'price' => $this->price,
            'category' => $this->category->value,
            'attributes' => $this->attributes,
            'createdAt' => $this->createdAt->format('c')
        ];
    }
}