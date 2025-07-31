<?php

namespace App\Repositories;

use App\Entities\Product;
use App\Enums\ProductCategory;
use PDO;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
class ProductRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {}

    public function save(Product $product): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (id, name, price, category, attributes, created_at)
            VALUES (:id, :name, :price, :category, :attributes, :created_at)
        ");

        $stmt->execute([
            ':id' => $product->id->toString(),
            ':name' => $product->name,
            ':price' => $product->price,
            ':category' => $product->category->value,
            ':attributes' => json_encode($product->attributes),
            ':created_at' => $product->createdAt->format('Y-m-d H:i:s')
        ]);
    }

    public function find(UuidInterface $id): ?Product
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id->toString()]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateProduct($data);
    }

    public function delete(UuidInterface $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id->toString()]);
    }

    public function update(Product $product): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET name = :name, price = :price, category = :category, attributes = :attributes
            WHERE id = :id
        ");

        $stmt->execute([
            ':id' => $product->id->toString(),
            ':name' => $product->name,
            ':price' => $product->price,
            ':category' => $product->category->value,
            ':attributes' => json_encode($product->attributes)
        ]);
    }

    public function findAll(?ProductCategory $category, ?float $minPrice, ?float $maxPrice): array
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if ($category !== null) {
            $sql .= " AND category = :category";
            $params[':category'] = $category->value;
        }

        if ($minPrice !== null) {
            $sql .= " AND price >= :min_price";
            $params[':min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $products = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = $this->hydrateProduct($data);
        }

        return $products;
    }

    private function hydrateProduct(array $data): Product
    {
        return new Product(
            Uuid::fromString($data['id']),
            $data['name'],
            (float)$data['price'],
            ProductCategory::from($data['category']),
            json_decode($data['attributes'], true),
            new \DateTimeImmutable($data['created_at'])
        );
    }
}