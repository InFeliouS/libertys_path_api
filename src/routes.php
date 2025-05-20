<?php
// src/routes.php

// start session if none
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($uri) {
    case '':
    case 'login':
        require __DIR__ . '/../public/html/login.html';
        break;

    case 'login/process':
        require __DIR__ . '/auth/login_process.php';
        break;

    case 'logout':
        require __DIR__ . '/auth/logout.php';
        break;

    case 'dashboard':
        require __DIR__ . '/dashboard/dashboard.php';
        break;

    case 'register':
        require __DIR__ . '/../public/html/register_student.html';
        break;

    case 'register/process':
        require __DIR__ . '/dashboard/student_register.php';
        break;

    case 'sections/create':
        require __DIR__ . '/../public/html/create_section.html';
        break;

    case 'sections/create/process':
        require __DIR__ . '/dashboard/create_section.php';
        break;

    case 'sections/update':
        require __DIR__ . '/dashboard/update_section.php';
        break;

    case 'sections/deleteSection':
        require __DIR__ . '/dashboard/delete_section.php';
        break;

    case 'sections/view':
        require __DIR__ . '/dashboard/section_detail.php';
        break;

    case 'students/update':
        require __DIR__ . '/dashboard/update_student.php';
        break;

    case 'sections/delete':
        require __DIR__ . '/dashboard/delete_students.php';
        break;

    case 'api/v1/student_info.php':
    case 'api/v1/student_info':
        require __DIR__ . '/api/v1/student_info.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../public/html/errors/404.html';
        break;
}
