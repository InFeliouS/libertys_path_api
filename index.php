<?php
// Load the Composer autoloader if needed
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';

    // Load environment variables (.env) if available
    if (class_exists('Dotenv\Dotenv') && file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

// Include database connection if needed
if (file_exists(__DIR__ . '/php/config/db.php')) {
    require_once __DIR__ . '/php/config/db.php';
}

// Get the request URI and remove the base path
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/libertys_path_api/';

// Remove the base path from the request URI if it exists
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

// Remove query string if present
if (($pos = strpos($request_uri, '?')) !== false) {
    $request_uri = substr($request_uri, 0, $pos);
}

// Remove trailing slash if present
$request_uri = rtrim($request_uri, '/');

// Default to home/index if no path is specified
if (empty($request_uri)) {
    $request_uri = 'login';
}

// Let the .htaccess handle the routing
// This file serves as a fallback in case direct access to index.php occurs
header("Location: $base_path$request_uri");
exit();
?>
