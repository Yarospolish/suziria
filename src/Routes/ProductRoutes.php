<?php

namespace App\Routes;

use App\Controllers\ProductController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class ProductRoutes
{
    public static function configure(App $app): void
    {
        $app->group('/api/products', function (RouteCollectorProxy $group) {
            $group->post('', [ProductController::class, 'createProduct']);
            $group->get('', [ProductController::class, 'listProducts']);
            $group->get('/{id}', [ProductController::class, 'getProduct']);
            $group->patch('/{id}', [ProductController::class, 'updateProduct']);
            $group->delete('/{id}', [ProductController::class, 'deleteProduct']);
        });
    }
}