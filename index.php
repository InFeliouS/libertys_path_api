<?php
// Load the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Load environment variables (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Include database connection
require_once __DIR__ . '/php/config/db.php';

// Handle routing (Basic Example)
$request_uri = trim($_SERVER['REQUEST_URI'], '/');

switch ($request_uri) {
    case '':
    case 'home':
        require_once __DIR__ . '/html/home.php';
        break;

    case 'login':
        require_once __DIR__ . '/html/auth/login.php';
        break;

    case 'dashboard':
        require_once __DIR__ . '/html/dashboard.php';
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/html/errors/404.php';
        break;
}

?>
