<?php

namespace Tests\Unit;

use App\DTO\CreateProductDTO;
use App\DTO\UpdateProductDTO;
use App\Enums\ProductCategory;
use App\Entities\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductServiceTest extends TestCase
{
    private ProductService $service;
    private ProductRepository $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }

    public function testCreateProduct(): void
    {
        $dto = new CreateProductDTO(
            'Test Product',
            100.50,
            ProductCategory::ELECTRONICS,
            ['color' => 'black']
        );

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Product::class));

        $product = $this->service->createProduct($dto);
        $this->assertEquals('Test Product', $product->name);
    }

    public function testUpdateProduct(): void
    {
        $uuid = Uuid::uuid4();
        $existingProduct = new Product(
            $uuid,
            'Old Name',
            50.00,
            ProductCategory::ELECTRONICS,
            ['color' => 'white'],
            new \DateTimeImmutable()
        );

        $this->repository->expects($this->once())
            ->method('find')
            ->with($uuid)
            ->willReturn($existingProduct);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($this->isInstanceOf(Product::class));

        $dto = new UpdateProductDTO(
            'New Name',
            75.00,
            null,
            null
        );

        $updatedProduct = $this->service->updateProduct($uuid->toString(), $dto);
        $this->assertEquals('New Name', $updatedProduct->name);
        $this->assertEquals(75.00, $updatedProduct->price);
    }
}