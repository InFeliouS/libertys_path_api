<?php
// src/routes.php — router for XAMPP without .htaccess
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$route = isset($_GET['r']) ? trim($_GET['r'], "/") : "";
if ($route === "" && (isset($_GET['route']) && $_GET['route'] !== "")) {
    $route = trim($_GET['route'], "/");
}
if ($route === "")
    $route = "login";

switch ($route) {
    case "login":
        require __DIR__ . "/../public/html/login.html";
        break;
    case "login/process":
        require __DIR__ . "/auth/login_process.php";
        break;
    case "logout":
        require __DIR__ . "/auth/logout.php";
        break;

    case "dashboard":
        require __DIR__ . "/dashboard/dashboard.php";
        break;

    case "sections/create":
        require __DIR__ . "/dashboard/create_section.php";
        break;
    case "sections/detail":
        require __DIR__ . "/dashboard/section_detail.php";
        break;
    case "sections/update":
        require __DIR__ . "/dashboard/update_section.php";
        break;
    case "sections/deleteSection":
        require __DIR__ . "/dashboard/delete_section.php";
        break;
    case "sections/download_csv":
        require __DIR__ . "/dashboard/download_section_students_csv.php";
        break;

    case "register":
        require __DIR__ . "/../public/html/register_student.html";
        break;
    case "register/process":
        require __DIR__ . "/dashboard/student_register.php";
        break;

    case "batch_upload":
        require __DIR__ . "/dashboard/batch_upload.php";
        break;

    case "questions/manage":
        require __DIR__ . "/../public/html/questions_manage.html";
        break;

    case "api/guard_questions/unity":
        // Unity-ready JSON endpoint
        require __DIR__ . "/../public/api/v1/guard_questions/unity_list.php";
        break;

    case "leaderboard":
        require __DIR__ . "/../public/html/leaderboard.html";
        break;

    // --- API: Leaderboard submit + top-by-section (so Unity can call index.php?r=...) ---
    case 'api/leaderboard/team/submit':
        require __DIR__ . '/../public/api/v1/leaderboard/team_submit.php';
        break;

    case 'api/leaderboard/team/top_by_section':
        require __DIR__ . '/../public/api/v1/leaderboard/team_top_by_section.php';
        break;



    default:
        http_response_code(404);
        $p = __DIR__ . "/../public/html/errors/404.html";
        if (file_exists($p)) {
            require $p;
        } else {
            echo "404";
        }
}
