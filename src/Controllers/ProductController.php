<?php

namespace App\Controllers;

use App\DTO\ApiResponseDTO;
use App\DTO\CreateProductDTO;
use App\DTO\ListProductsDTO;
use App\DTO\UpdateProductDTO;
use App\Exceptions\ProductNotFoundException;
use App\Services\ProductService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    private function jsonResponse(
        Response $response,
        bool $success,
        mixed $data = null,
        ?string $error = null,
        int $statusCode = 200
    ): Response {
        $responseDto = new ApiResponseDTO($success, $data, $error);
        $response->getBody()->write(json_encode($responseDto->toArray()));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    public function createProduct(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if ($data === null) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                'Invalid JSON data provided',
                400
            );
        }

        try {
            $dto = CreateProductDTO::fromRequest($data);
            $product = $this->productService->createProduct($dto);

            return $this->jsonResponse(
                $response,
                true,
                $product->toArray(),
                null,
                201
            );
        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                400
            );
        }
    }

    public function getProduct(Response $response, array $args): Response
    {
        try {
            $product = $this->productService->getProduct($args['id']);
            return $this->jsonResponse(
                $response,
                true,
                $product->toArray(),
                null,
                200
            );
        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                400
            );
        } catch (ProductNotFoundException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                404
            );
        }
    }

    public function updateProduct(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $dto = UpdateProductDTO::fromRequest($data);
            $product = $this->productService->updateProduct($args['id'], $dto);

            return $this->jsonResponse(
                $response,
                true,
                $product->toArray(),
                null,
                200
            );
        } catch (ProductNotFoundException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                404
            );
        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                400
            );
        }
    }

    public function deleteProduct(Response $response, array $args): Response
    {
        try {
            $this->productService->deleteProduct($args['id']);
            return $response->withStatus(204);
        } catch (ProductNotFoundException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                404
            );
        }
    }

    public function listProducts(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $dto = ListProductsDTO::fromQueryParams($queryParams);
            $products = $this->productService->listProducts($dto);

            return $this->jsonResponse(
                $response,
                true,
                array_map(fn($p) => $p->toArray(), $products),
                null,
                200
            );
        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse(
                $response,
                false,
                null,
                $e->getMessage(),
                400
            );
        }
    }
}