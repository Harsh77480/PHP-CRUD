<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    // for cors 
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    $app->get('/users', function (Request $request, Response $response) {
        try {
            $pdo = getPDOConnection();
            $stmt = $pdo->query("SELECT id, name FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                
            $response->getBody()->write(json_encode($users));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Internal Server Error']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });
    
    $app->post('/users', function (Request $request, Response $response) {
        
            $pdo = getPDOConnection();
            $data = json_decode($request->getBody(), true);
    
            if (!isset($data['name'], $data['email'], $data['hobby'])) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400)
                    ->getBody()->write(json_encode(['error' => 'Invalid input data']));
            }
    
            $stmt = $pdo->prepare("INSERT INTO users (name, email, hobby) VALUES (:name, :email, :hobby)");
            $stmt->execute(['name' => $data['name'], 'email' => $data['email'], 'hobby' => $data['hobby']]);
    
            $response->getBody()->write(json_encode(['status' => 'User added']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

    
    });
    
    $app->put('/users/{id}', function (Request $request, Response $response, $args) {
        try {
            $pdo = getPDOConnection();
            $data = json_decode($request->getBody(), true);
    
            if (!isset($data['name'], $data['email'], $data['hobby'])) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400)
                    ->getBody()->write(json_encode(['error' => 'Invalid input data']));
            }
    
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, hobby = :hobby WHERE id = :id");
            $stmt->execute(['name' => $data['name'], 'email' => $data['email'], 'hobby' => $data['hobby'], 'id' => $args['id']]);
    
            if ($stmt->rowCount() === 0) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404)
                    ->getBody()->write(json_encode(['error' => 'User not found']));
            }
            
            $response->getBody()->write(json_encode(['status' => 'User updated']));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Internal Server Error']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
                
        }
    });
    
    $app->delete('/users/{id}', function (Request $request, Response $response, $args) {
        try {
            $pdo = getPDOConnection();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $args['id']]);
    
            if ($stmt->rowCount() === 0) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404)
                    ->getBody()->write(json_encode(['error' => 'User not found']));
            }
    
            $response->getBody()->write(json_encode(['status' => 'User deleted']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Internal Server Error']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
                
        }
    });
    
    $app->get('/users/{id}', function (Request $request, Response $response, $args) {
        try {
            $pdo = getPDOConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $args['id'], PDO::PARAM_INT);
            $stmt->execute();
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user) {
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404)
                    ->getBody()->write(json_encode(['error' => 'User not found']));
            }
            $response->getBody()->write(json_encode($user));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Internal Server Error']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
                
        }
    });

    
};



