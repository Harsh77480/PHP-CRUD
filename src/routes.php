<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Illuminate\Database\Capsule\Manager as Capsule; // Add this line

return function (App $app) {

    // for cors 
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    
    $app->get('/users', function (Request $request, Response $response) {
        $pdo = getPDOConnection();

        $stmt = $pdo->query("SELECT id,name FROM users");
        $users = $stmt->fetchAll();
        // error_log(json_encode($users)); // for debugging 

        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/users', function (Request $request, Response $response) {
        $pdo = getPDOConnection();
        $data = json_decode($request->getBody(), true);
        $stmt = $pdo->prepare("INSERT INTO users (name,email,hobby) VALUES (:name,:email,:hobby)");
        $stmt->execute(['name' => $data['name'],'email'=>$data['email'],'hobby'=>$data['hobby']]);
        $response->getBody()->write(json_encode(['status' => 'User added']));
        return $response->withHeader('Content-Type', 'application/json');
    });


    $app->put('/users/{id}', function (Request $request, Response $response, $args) {
        $pdo = getPDOConnection();
        $data = json_decode($request->getBody(), true);
        $stmt = $pdo->prepare("UPDATE users SET name = :name,email = :email, hobby = :hobby WHERE id = :id");
        $stmt->execute(['name' => $data['name'],'email' => $data['email'],'hobby' => $data['hobby'], 'id' => $args['id']]);
        $response->getBody()->write(json_encode(['status' => 'Item updated']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->delete('/users/{id}', function (Request $request, Response $response, $args) {
        $pdo = getPDOConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $args['id']]);
        $response->getBody()->write(json_encode(['status' => 'Item deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/users/{id}', function (Request $request, Response $response,$args) {
        $pdo = getPDOConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id',$args['id'], PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    });


};
