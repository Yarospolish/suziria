<?php

namespace Tests\Integration;

use App\Utils\Database;
use Dotenv\Dotenv;
use PDO;
use PHPUnit\Framework\TestCase;

class ProductIntegrationTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        $this->pdo = (new Database(
            $_ENV['TEST_DB_HOST'] ?? 'localhost',
            $_ENV['TEST_DB_NAME'] ?? 'products',
            $_ENV['TEST_DB_USER'] ?? 'user',
            $_ENV['TEST_DB_PASSWORD'] ?? 'test'
        ))->getConnection();

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS products (
            id UUID PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            category VARCHAR(50) NOT NULL,
            attributes JSONB NOT NULL,
            created_at TIMESTAMP NOT NULL
        )');

        $this->pdo->exec('DELETE FROM products');
    }

    public function testProductLifecycle(): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO products (id, name, price, category, attributes, created_at)
            VALUES (:id, :name, :price, :category, :attributes, :created_at)
        ');

        $id = '550e8400-e29b-41d4-a716-446655440000';
        $stmt->execute([
            ':id' => $id,
            ':name' => 'Integration Test Product',
            ':price' => 99.99,
            ':category' => 'electronics',
            ':attributes' => json_encode(['color' => 'blue']),
            ':created_at' => '2023-01-01 12:00:00'
        ]);

        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Integration Test Product', $product['name']);
        $this->assertEquals(99.99, (float)$product['price']);
        $this->assertEquals('electronics', $product['category']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS products');
    }
}