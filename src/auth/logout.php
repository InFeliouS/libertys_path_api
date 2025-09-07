<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

if (!function_exists('route_to')) {
    function route_to(string $path): string {
        $basePath = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');
        $baseUrl  = $basePath === '' ? '/' : $basePath . '/';
        return $baseUrl . 'index.php?route=' . ltrim($path, '/');
    }
}

header('Location: ' . route_to('login'));
exit;
