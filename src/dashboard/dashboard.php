<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();

if (!function_exists('route_to')) {
    function route_to(string $path): string {
        $bp = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');
        $base = $bp === '' ? '/' : $bp . '/';
        return $base . 'index.php?route=' . ltrim($path, '/');
    }
}

if (empty($_SESSION['teacher_id'])) {
    header('Location: ' . route_to('login'));
    exit;
}

require __DIR__ . '/../../public/html/dashboard_view.html';
