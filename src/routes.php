<?php
/**
 * Router for LIBERTYS_PATH_API
 * Inputs from index.php:
 *   - $route (string)  e.g. "/login"
 *   - $method (string) "GET" | "POST"
 *   - $__APP (array)   env/debug/base/public/base_path
 */

if (!isset($route, $method, $__APP)) {
    http_response_code(500);
    exit('Router missing context.');
}

/* ───────────── Helpers ───────────── */

function base_url(): string {
    global $__APP;
    $bp = rtrim((string)($__APP['base_path'] ?? ''), '/');
    return $bp === '' ? '/' : $bp . '/';
}

/** Include an HTML view from /public/html and inject <base href> */
function include_view(string $relPath): void {
    global $__APP;

    $fullPath = ($__APP['public'] ?? __DIR__) . '/html/' . ltrim($relPath, '/');
    if (!is_file($fullPath)) {
        http_response_code(404);
        echo "View not found: html/{$relPath}";
        return;
    }

    $html = file_get_contents($fullPath);
    if ($html === false) {
        http_response_code(500);
        echo "Failed to read view: html/{$relPath}";
        return;
    }

    $baseHref = htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8');

    if (preg_match('/<head[^>]*>/i', $html)) {
        $html = preg_replace(
            '/(<head[^>]*>)/i',
            '$1' . "\n" . '<base href="' . $baseHref . '">',
            $html,
            1
        );
    } else {
        $html = '<base href="' . $baseHref . '">' . "\n" . $html;
    }

    header('Content-Type: text/html; charset=utf-8');
    echo $html;
}

/** Require a PHP script from /src */
function require_src(string $relPath): void {
    global $__APP;
    $fullPath = ($__APP['base'] ?? __DIR__) . '/src/' . ltrim($relPath, '/');
    if (!is_file($fullPath)) {
        http_response_code(500);
        echo "Server file missing: src/{$relPath}";
        return;
    }
    require $fullPath;
}

/** 404 fallback */
function not_found(string $r): void {
    global $__APP;
    http_response_code(404);
    $pretty404 = ($__APP['public'] ?? __DIR__) . '/html/errors/404.html';
    if (is_file($pretty404)) {
        readfile($pretty404);
        return;
    }
    header('Content-Type: text/plain; charset=utf-8');
    echo "404 Not Found: {$r}";
}

/* ───────────── Routes ───────────── */

switch (true) {
    // Home → Login
    case $method === 'GET' && ($route === '/' || $route === '/home'):
        header('Location: ' . base_url() . 'login');
        exit;

    // Auth
    case $method === 'GET' && $route === '/login':
        include_view('login.html');                 // public/html/login.html
        break;

    case $method === 'POST' && $route === '/login/process':
    case $method === 'POST' && $route === '/auth/login': // support either path
        require_src('auth/login_process.php');      // src/auth/login_process.php
        break;

    case $method === 'GET' && $route === '/auth/logout':
        require_src('auth/logout.php');             // src/auth/logout.php
        break;

    // Dashboard
    case $method === 'GET' && $route === '/dashboard':
        require_src('dashboard/dashboard.php');     // src/dashboard/dashboard.php
        break;

    // Sections
    case $method === 'GET' && $route === '/sections/create':
        include_view('create_section.html');        // public/html/create_section.html
        break;

    case $method === 'POST' && $route === '/sections/create':
        require_src('dashboard/create_section.php');
        break;

    case $method === 'GET' && preg_match('#^/sections/(\d+)$#', $route, $m):
        $_GET['id'] = $m[1];
        require_src('dashboard/section_detail.php');
        break;

    case $method === 'POST' && preg_match('#^/sections/(\d+)/update$#', $route, $m):
        $_POST['id'] = $m[1];
        require_src('dashboard/update_section.php');
        break;

    case $method === 'POST' && preg_match('#^/sections/(\d+)/delete$#', $route, $m):
        $_POST['id'] = $m[1];
        require_src('dashboard/delete_section.php');
        break;

    case $method === 'POST' && preg_match('#^/sections/(\d+)/students/delete$#', $route, $m):
        $_POST['section_id'] = $m[1];
        require_src('dashboard/delete_students.php');
        break;

    case $method === 'GET' && preg_match('#^/sections/(\d+)/download$#', $route, $m):
        $_GET['section_id'] = $m[1];
        require_src('dashboard/download_section_students_csv.php');
        break;

    // Students
    case $method === 'POST' && $route === '/students/register':
        require_src('dashboard/student_register.php');
        break;

    case $method === 'POST' && $route === '/students/update':
        require_src('dashboard/update_student.php');
        break;

    // Batch upload
    case $method === 'POST' && $route === '/batch_upload':
        require_src('dashboard/batch_upload.php');
        break;

    // Health
    case $method === 'GET' && $route === '/health':
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'env' => $__APP['env'] ?? 'unknown']);
        break;

    // Fallback
    default:
        not_found($route);
        break;
}
