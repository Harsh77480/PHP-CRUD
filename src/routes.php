<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Illuminate\Database\Capsule\Manager as Capsule; // Add this line

return function (App $app) {

    // // for cors 
    // $app->options('/{routes:.+}', function (Request $request, Response $response) {
    //     return $response
    //         ->withHeader('Access-Control-Allow-Origin', '*')
    //         ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    //         ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    // });

    $app->get('/items', function (Request $request, Response $response) {
        error_log("GET /items route hit!");
        
        $pdo = getPDOConnection();
        $stmt = $pdo->query("SELECT * FROM items");
        $items = $stmt->fetchAll();
        $response->getBody()->write(json_encode($items));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/items', function (Request $request, Response $response) {
        $pdo = getPDOConnection();
        $data = json_decode($request->getBody(), true);
        $stmt = $pdo->prepare("INSERT INTO items (name) VALUES (:name)");
        $stmt->execute(['name' => $data['name']]);
        $response->getBody()->write(json_encode(['status' => 'Item added']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->put('/items/{id}', function (Request $request, Response $response, $args) {
        $pdo = getPDOConnection();
        $data = json_decode($request->getBody(), true);
        $stmt = $pdo->prepare("UPDATE items SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $data['name'], 'id' => $args['id']]);
        $response->getBody()->write(json_encode(['status' => 'Item updated']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/items/{id}', function (Request $request, Response $response, $args) {
        $pdo = getPDOConnection();
        $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
        $stmt->execute(['id' => $args['id']]);
        $response->getBody()->write(json_encode(['status' => 'Item deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    });
};
