<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('route_to')) {
    function route_to(string $path): string {
        $basePath = rtrim((string)($_ENV['BASE_PATH'] ?? ''), '/');
        $baseUrl  = $basePath === '' ? '/' : $basePath . '/';
        return $baseUrl . 'index.php?route=' . ltrim($path, '/');
    }
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: ' . route_to('login') . '&err=empty');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$row = null;
if (isset($mysqli)) {
    $stmt = $mysqli->prepare('SELECT id, username, password FROM teachers WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
}

if (!$row) {
    header('Location: ' . route_to('login') . '&err=nouser');
    exit;
}

// ✅ verify bcrypt hash
if (!password_verify($password, $row['password'])) {
    header('Location: ' . route_to('login') . '&err=badpass');
    exit;
}

// Success
$_SESSION['teacher_id']   = (int)$row['id'];
$_SESSION['teacher_name'] = $row['username'];
session_regenerate_id(true);

header('Location: ' . route_to('dashboard'));
exit;
