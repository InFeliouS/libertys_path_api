<?php
declare(strict_types=1);

/**
 * Front controller tailored to your structure:
 *
 * LIBERTYS_PATH_API/
 *  ├─ public/           (document root)
 *  │   ├─ api/v1/*.php  (direct PHP endpoints - bypass router)
 *  │   ├─ html/*.html   (views)
 *  │   ├─ css/, js/, assets/
 *  │   └─ index.php     (this file)
 *  ├─ src/
 *  │   ├─ auth/*.php
 *  │   ├─ config/db.php
 *  │   ├─ dashboard/*.php
 *  │   ├─ utils/*.php
 *  │   └─ routes.php    (router below)
 *  ├─ vendor/           (composer)
 *  └─ .env              (optional)
 */

$BASE   = dirname(__DIR__);   // project root
$PUBLIC = __DIR__;            // .../public

// ─────────────────────────────────────────────────────────────────────────────
// Composer autoload (required)
$autoload = $BASE . '/vendor/autoload.php';
if (!is_file($autoload)) {
    http_response_code(500);
    exit('Missing vendor/autoload.php. Run `composer install` in project root.');
}
require $autoload;

// Load .env if available (optional for prod if you set real env vars)
if (class_exists('Dotenv\\Dotenv') && is_file($BASE . '/.env')) {
    Dotenv\Dotenv::createImmutable($BASE)->load();
}

// Basic PHP ini
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Manila');
$debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

// ─────────────────────────────────────────────────────────────────────────────
// Base path support (if deploying under a subfolder, e.g. /libertys_path_api)
$BASE_PATH = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');

// Decide routing mode:
// - local/dev: allow index.php?route=/path
// - prod: use REQUEST_URI (pretty URLs via .htaccess / nginx)
$env = ($_ENV['APP_ENV'] ?? 'production');

function detect_route(string $env, string $basePath): string {
    if ($env === 'local') {
        $r = $_GET['route'] ?? '/';
        return '/' . ltrim($r, '/');
    }
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($basePath !== '' && $basePath !== '/') {
        if (stripos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            if ($uri === '' || $uri === false) { $uri = '/'; }
        }
    }
    return '/' . ltrim($uri, '/');
}

$route  = detect_route($env, $BASE_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ─────────────────────────────────────────────────────────────────────────────
// Let real files under /public/api/v1/ bypass the router (direct endpoints)
if (strncmp($route, '/api/', 5) === 0) {
    // If user came through pretty URL, this request should have hit a real file.
    // In dev (when calling explicitly), they’ll access /public/api/v1/*.php directly.
    // We deliberately DO NOTHING here so Apache/Nginx (or direct URL) serves it.
    // If someone somehow hits /api/... through the router, show 404 to be explicit.
    http_response_code(404);
    exit('API endpoint not found through router. Call /public/api/v1/*.php directly.');
}

// ─────────────────────────────────────────────────────────────────────────────
// Small app context shared with routes.php if needed
$__APP = [
    'env'       => $env,
    'debug'     => $debug,
    'base'      => $BASE,
    'public'    => $PUBLIC,
    'base_path' => $BASE_PATH,
];

// Hand off to router (reads $route, $method, $__APP)
require $BASE . '/src/routes.php';
