<?php
// src/routes.php — router for XAMPP without .htaccess
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* --- tiny guard helpers (if present) --- */
$__guard = __DIR__ . "/auth/auth_guard.php";
if (is_file($__guard)) {
    require_once $__guard;
}
if (!function_exists('require_auth')) {
    function require_auth()
    {
    }
}
if (!function_exists('enforce_section_scope')) {
    function enforce_section_scope($id)
    {
    }
}
if (!function_exists('is_admin')) {
    function is_admin()
    {
        return false;
    }
}

/* --- resolve route (unchanged) --- */
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
        // 🔒 protected
        require_auth();
        require __DIR__ . "/dashboard/dashboard.php";
        break;

    /* =========================
       ADMIN: TEACHERS (NEW)
       ========================= */
    case "teachers/create":
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/../public/html/teachers_create.html";
        break;

    case "teachers/view":
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/../public/html/teachers_view.html";
        break;

    case "teachers/store":
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/dashboard/teachers_store.php";
        break;

    case 'api/v1/teachers_list':
        // 🔒 protected + admin-only
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . '/../public/api/v1/teachers_list.php';
        break;



    /* =========================
       ADMIN: SECTIONS (existing)
       ========================= */
    case "sections/create":
        // 🔒 protected + admin-only
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/dashboard/create_section.php";
        break;

    case "sections/detail":
        // 🔒 protected + section-scoped
        require_auth();
        $sectionId = (int) ($_GET['id'] ?? $_GET['section_id'] ?? 0);
        if ($sectionId > 0) {
            enforce_section_scope($sectionId);
        }
        require __DIR__ . "/dashboard/section_detail.php";
        break;

    case "sections/update":
        // 🔒 protected + admin-only
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/dashboard/update_section.php";
        break;

    case "sections/deleteSection":
        // 🔒 protected + admin-only
        require_auth();
        if (!is_admin()) {
            http_response_code(403);
            exit('Admins only');
        }
        require __DIR__ . "/dashboard/delete_section.php";
        break;

    case "sections/delete_students":
        // 🔒 protected (only logged-in teachers or admins)
        require_auth();
        require __DIR__ . "/dashboard/delete_students.php";
        break;


    case "questions/manage":
        require_auth();
        require __DIR__ . "/../public/html/questions_manage.html";
        break;

    /* =========================
       PUBLIC API (Unity)
       ========================= */
    case "api/guard_questions/unity":
        require __DIR__ . "/../public/api/v1/guard_questions/unity_list.php";
        break;

    case "leaderboard":
        require_auth();
        require __DIR__ . "/../public/html/leaderboard.html";
        break;

    // --- API: Leaderboard submit + top-by-section (public for game) ---
    case 'api/leaderboard/team/submit':
        require __DIR__ . '/../public/api/v1/leaderboard/team_submit.php';
        break;

    case 'api/leaderboard/team/top_by_section':
        require __DIR__ . '/../public/api/v1/leaderboard/team_top_by_section.php';
        break;

    case 'api/student/register':
        require __DIR__ . '/../public/api/v1/student_register.php';
        break;

    case "api/register_student_sections":
        require __DIR__ . '/../public/api/v1/register_student_sections.php';
        break;


    case 'api/student/pair_login':
        require __DIR__ . '/../public/api/v1/pair_login.php';
        break;

    case 'api/v1/teacher_config':
        require __DIR__ . '/../public/api/v1/teacher_config.php';
        break;

    case 'api/v1/unity_run_config':
        require __DIR__ . '/../public/api/v1/unity_run_config.php';
        break;

    case 'api/v1/unity_run_difficulty':
        require __DIR__ . '/../public/api/v1/unity_run_difficulty.php';
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
