<?php

namespace App\Utils;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct(
        string $host,
        string $dbname,
        string $username,
        string $password
    ) {
        try {
            $this->pdo = new PDO(
                "pgsql:host=$host;dbname=$dbname",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}