<?php

use Slim\Factory\AppFactory;
use App\Middleware\ErrorHandlerMiddleware;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

$app = AppFactory::create();

// Adiciona o middleware de tratamento de erros personalizado
$app->add(new ErrorHandlerMiddleware());

// Middleware para CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

require __DIR__ . '/../src/routes.php';

$app->run(); 