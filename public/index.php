<?php
declare(strict_types=1);

$BASE   = dirname(__DIR__);
$PUBLIC = __DIR__;

// Composer autoload
$autoload = $BASE . '/vendor/autoload.php';
if (!is_file($autoload)) { http_response_code(500); exit('Missing vendor/autoload.php'); }
require $autoload;

// .env
if (class_exists('Dotenv\\Dotenv') && is_file($BASE . '/.env')) {
    Dotenv\Dotenv::createImmutable($BASE)->load();
}

date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Manila');
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

// For subfolder local path (e.g., /libertys_path_api/public)
$BASE_PATH = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');

// Route detection
function detect_route(string $basePath): string {
    // Always honor ?route= for Option A
    if (isset($_GET['route']) && $_GET['route'] !== '') {
        return '/' . ltrim($_GET['route'], '/');
    }
    // Pretty URLs (in case you turn rewrites on later)
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($basePath !== '' && $basePath !== '/') {
        if (stripos($uri, $basePath) === 0) { $uri = substr($uri, strlen($basePath)) ?: '/'; }
    }
    return '/' . ltrim($uri, '/');
}

$route  = detect_route($BASE_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Prevent calling /api/* through the router (your API PHP files are direct)
if (strncmp($route, '/api/', 5) === 0) {
    http_response_code(404);
    exit('API endpoint not found through router. Call /public/api/v1/*.php directly.');
}

$__APP = [
    'env'       => $_ENV['APP_ENV'] ?? 'production',
    'debug'     => $debug,
    'base'      => $BASE,
    'public'    => $PUBLIC,
    'base_path' => $BASE_PATH,
];

require $BASE . '/src/routes.php';
