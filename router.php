<?php
// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if (file_exists(__DIR__ . $uri)) {
    return false;
}

// Include the routes file
require __DIR__ . '/src/routes.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);