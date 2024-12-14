<?php
function getPDOConnection() {
    $host = 'sql12.freesqldatabase.com';
    $db   = 'sql12751991';
    $user = 'sql12751991';
    $pass = 'rBPJDMv5Hx';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {


        $pdo =  new PDO($dsn, $user, $pass, $options);

        $sql = "
        CREATE TABLE IF NOT EXISTS items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        );";

        
    // Execute the query
    $pdo->exec($sql);

    return $pdo;
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
