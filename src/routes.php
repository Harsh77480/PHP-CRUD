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
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->getBody()->write(json_encode($users));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()->write(json_encode(['error' => 'Internal Server Error']));
        }
    });
    
    $app->post('/users', function (Request $request, Response $response) {
        try {
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
    
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201)
                ->getBody()->write(json_encode(['status' => 'User added']));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()->write(json_encode(['error' => 'Internal Server Error']));
        }
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
    
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->getBody()->write(json_encode(['status' => 'User updated']));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()->write(json_encode(['error' => 'Internal Server Error']));
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
    
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->getBody()->write(json_encode(['status' => 'User deleted']));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()->write(json_encode(['error' => 'Internal Server Error']));
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
    
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200)
                ->getBody()->write(json_encode($user));
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500)
                ->getBody()->write(json_encode(['error' => 'Internal Server Error']));
        }
    });
    
};



