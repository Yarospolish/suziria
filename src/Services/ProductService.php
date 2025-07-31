<?php

namespace App\Services;

use App\DTO\CreateProductDTO;
use App\DTO\ListProductsDTO;
use App\DTO\ProductDTO;
use App\DTO\UpdateProductDTO;
use App\Entities\Product;
use App\Exceptions\ProductNotFoundException;
use App\Repositories\ProductRepository;
use Ramsey\Uuid\Uuid;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    public function createProduct(CreateProductDTO $dto): ProductDTO
    {
        $product = new Product(
            Uuid::uuid4(),
            $dto->name,
            $dto->price,
            $dto->category,
            $dto->attributes,
            new \DateTimeImmutable()
        );

        $this->productRepository->save($product);

        return new ProductDTO(
            $product->id,
            $product->name,
            $product->price,
            $product->category,
            $product->attributes,
            $product->createdAt
        );
    }

    public function getProduct(string $id): ProductDTO
    {
        $uuid = Uuid::fromString($id);
        $product = $this->productRepository->find($uuid);

        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        return new ProductDTO(
            $product->id,
            $product->name,
            $product->price,
            $product->category,
            $product->attributes,
            $product->createdAt
        );
    }

    public function updateProduct(string $id, UpdateProductDTO $dto): ProductDTO
    {
        $uuid = Uuid::fromString($id);
        $existingProduct = $this->productRepository->find($uuid);

        if ($existingProduct === null) {
            throw new ProductNotFoundException($id);
        }

        $updatedProduct = new Product(
            $existingProduct->id,
            $dto->name ?? $existingProduct->name,
            $dto->price ?? $existingProduct->price,
            $dto->category ?? $existingProduct->category,
            $dto->attributes ?? $existingProduct->attributes,
            $existingProduct->createdAt
        );

        $this->productRepository->update($updatedProduct);

        return new ProductDTO(
            $updatedProduct->id,
            $updatedProduct->name,
            $updatedProduct->price,
            $updatedProduct->category,
            $updatedProduct->attributes,
            $updatedProduct->createdAt
        );
    }

    public function deleteProduct(string $id): void
    {
        $uuid = Uuid::fromString($id);
        $product = $this->productRepository->find($uuid);

        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        $this->productRepository->delete($uuid);
    }

    /**
     * @return ProductDTO[]
     */
    public function listProducts(ListProductsDTO $dto): array
    {
        $products = $this->productRepository->findAll(
            $dto->category,
            $dto->minPrice,
            $dto->maxPrice
        );

        return array_map(
            fn(Product $product) => new ProductDTO(
                $product->id,
                $product->name,
                $product->price,
                $product->category,
                $product->attributes,
                $product->createdAt
            ),
            $products
        );
    }
}