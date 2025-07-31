<?php

namespace Tests\Unit;

use App\Enums\ProductCategory;
use App\Entities\Product;
use App\Repositories\ProductRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repository;
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->repository = new ProductRepository($this->pdo);
    }

    public function testSaveProduct(): void
    {
        $product = new Product(
            Uuid::uuid4(),
            'Test Product',
            100.50,
            ProductCategory::ELECTRONICS,
            ['color' => 'black'],
            new \DateTimeImmutable()
        );

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($params) {
                return is_array($params) && count($params) === 6;
            }));

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $this->repository->save($product);
    }

    public function testFindProduct(): void
    {
        $uuid = Uuid::uuid4();
        $expectedData = [
            'id' => $uuid->toString(),
            'name' => 'Test Product',
            'price' => 100.50,
            'category' => 'electronics',
            'attributes' => json_encode(['color' => 'black']),
            'created_at' => '2023-01-01 12:00:00'
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([':id' => $uuid->toString()]);
        $stmt->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedData);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $product = $this->repository->find($uuid);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->name);
    }
}