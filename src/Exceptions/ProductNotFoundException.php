<?php

namespace App\Exceptions;

use Exception;
use Ramsey\Uuid\UuidInterface;

class ProductNotFoundException extends Exception
{
    public function __construct(UuidInterface|string $id)
    {
        $idString = is_string($id) ? $id : $id->toString();
        parent::__construct("Product with ID {$idString} not found", 404);
    }
}