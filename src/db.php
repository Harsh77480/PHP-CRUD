<?php
function getPDOConnection() {
    $host = $_ENV['HOST'];
    $db   = $_ENV['DB'];
    $user = $_ENV['USER'];
    $pass = $_ENV['PASS'];
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {

        $pdo =  new PDO($dsn, $user, $pass, $options);

        // $sql = "
        // CREATE TABLE IF NOT EXISTS items (
        //     id INT AUTO_INCREMENT PRIMARY KEY,
        //     name VARCHAR(255) NOT NULL
        // );";

        // $sql = "
        // CREATE TABLE IF NOT EXISTS users (
        // id INT AUTO_INCREMENT PRIMARY KEY,
        // name VARCHAR(255) NOT NULL,
        // email VARCHAR(255) NOT NULL,
        // hobby VARCHAR(255) NOT NULL);
        // ";

        // $sql = "
        // INSERT INTO users (name, email, hobby) 
        // VALUES ('Harsh', 'h@gmail.com', 'coding');
        // ";

    // $pdo->exec($sql);

    return $pdo;
    } catch (\PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit; 
    }
}
