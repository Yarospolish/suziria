<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Routes\ProductRoutes;
use App\Utils\Database;
use DI\Container;
use Slim\Factory\AppFactory;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();

$container->set(PDO::class, function () {
    $db = new Database(
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_NAME'] ?? 'products',
        $_ENV['DB_USER'] ?? 'user',
        $_ENV['DB_PASSWORD'] ?? 'test'
    );
    return $db->getConnection();
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

ProductRoutes::configure($app);

$app->addErrorMiddleware(true, true, true);

$app->run();