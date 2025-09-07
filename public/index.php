<?php
declare(strict_types=1);

/**
 * Front controller for LIBERTYS_PATH_API
 * - Works with or without .htaccess
 * - Always honors ?route=... (great for debugging)
 */

$BASE   = dirname(__DIR__);   // project root
$PUBLIC = __DIR__;            // .../public

// ── Composer autoload ─────────────────────────────────────────────
$autoload = $BASE . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    exit('Missing vendor/autoload.php. Run `composer install`.');
}
require $autoload;

// ── .env (optional) ───────────────────────────────────────────────
if (class_exists('Dotenv\\Dotenv') && is_file($BASE . '/.env')) {
    Dotenv\Dotenv::createImmutable($BASE)->load();
}

date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Manila');
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

// ── Base path (for subfolder installs) ────────────────────────────
// Example for your current local URL:
//   http://localhost/libertys_path_api/public/index.php?route=login
// set BASE_PATH=/libertys_path_api/public in .env
$BASE_PATH = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');

// ── Route detection ───────────────────────────────────────────────
function detect_route(string $basePath): string {
    // Always honor ?route=... (works in dev & prod)
    if (isset($_GET['route']) && $_GET['route'] !== '') {
        return '/' . ltrim($_GET['route'], '/');
    }
    // Otherwise derive from REQUEST_URI (pretty URLs)
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($basePath !== '' && $basePath !== '/') {
        if (stripos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }
    }
    return '/' . ltrim($uri, '/');
}

$route  = detect_route($BASE_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ── If someone tries /api/* through the router, 404 it ────────────
// (Your /public/api/v1/*.php files should be accessed directly.)
if (strncmp($route, '/api/', 5) === 0) {
    http_response_code(404);
    exit('API endpoint not found through router. Call /public/api/v1/*.php directly.');
}

// ── App context shared with routes.php ────────────────────────────
$__APP = [
    'env'       => $_ENV['APP_ENV'] ?? 'production',
    'debug'     => $debug,
    'base'      => $BASE,
    'public'    => $PUBLIC,
    'base_path' => $BASE_PATH,
];

// ── Hand off to the router ────────────────────────────────────────
require $BASE . '/src/routes.php';
