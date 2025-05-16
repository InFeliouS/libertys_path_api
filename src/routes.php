<?php
// src/routes.php

// Only start session if one isn’t already active (we already did it in public/index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Capture the path
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Define some base paths
$baseDir    = __DIR__ . '/..';           // …/libertys_path_api/src/..
$srcDir     = $baseDir . '/src';         // …/libertys_path_api/src
$publicHtml = $baseDir . '/public/html'; // …/libertys_path_api/public/html

switch ($uri) {
    case '':
    case 'login':
        include $publicHtml . '/login.html';
        break;

    case 'login/process':
        require $srcDir . '/auth/login_process.php';
        break;

    case 'logout':
        require $srcDir . '/auth/logout.php';
        break;

    case 'register':
        include $publicHtml . '/register_student.html';
        break;

    case 'register/process':
        require $srcDir . '/dashboard/student_register.php';
        break;

    case 'dashboard':
        require $srcDir . '/dashboard/dashboard.php';
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        include $publicHtml . '/errors/404.html';
        break;
}
