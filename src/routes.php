<?php
// src/routes.php

// Ensure session is started (public/index.php already invoked start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Directory where your HTML fragments live
$publicHtml = __DIR__ . '/../public/html';

// Parse the request URI (strip base path & leading/trailing slashes)
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    // Login
    case '':
    case 'login':
        include $publicHtml . '/login.html';
        break;
    case 'login/process':
        require __DIR__ . '/auth/login_process.php';
        break;

    // Logout
    case 'logout':
        require __DIR__ . '/auth/logout.php';
        break;

    // Register
    case 'register':
        include $publicHtml . '/register_student.html';
        break;
    case 'register/process':
        require __DIR__ . '/dashboard/student_register.php';
        break;

    // Dashboard
    case 'dashboard':
        require __DIR__ . '/dashboard/dashboard.php';
        break;

    // View one Section’s students
    case 'sections/view':
        require __DIR__ . '/dashboard/section_detail.php';
        break;
        
    // Create Section (both GET & POST handled in same script)
    case 'sections/create':
    case 'sections/create/process':
        require __DIR__ . '/dashboard/create_section.php';
        break;

    // 404 fallback
    default:
        header("HTTP/1.0 404 Not Found");
        include $publicHtml . '/errors/404.html';
        break;
}
