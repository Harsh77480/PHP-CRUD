<?php
require __DIR__ . '/../vendor/autoload.php';

// Load .env file
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();



require __DIR__ . '/../src/db.php';


use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Add CORS middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
});

// Include Routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);


$app->run();
