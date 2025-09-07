<?php
/**
 * Router for LIBERTYS_PATH_API, aligned to your folders.
 *
 * Reads:
 *  - $route   (string, e.g. "/login", "/dashboard", "/sections/123")
 *  - $method  (string, "GET" | "POST" | ...)
 *  - $__APP   (array: env/debug/base/public/base_path)
 *
 * Static assets and /public/api/v1/*.php are NOT routed here.
 */

if (!isset($route, $method, $__APP)) {
    http_response_code(500);
    exit('Router missing context.');
}

// Helpers (safe includes)
function include_view(string $relPath): void {
    global $__APP;
    $file = $__APP['public'] . '/html/' . ltrim($relPath, '/');
    if (is_file($file)) { require $file; return; }
    http_response_code(404);
    echo "View not found: html/{$relPath}";
}

function require_src(string $relPath): void {
    global $__APP;
    $file = $__APP['base'] . '/src/' . ltrim($relPath, '/');
    if (is_file($file)) { require $file; return; }
    http_response_code(500);
    echo "Server file missing: src/{$relPath}";
}

function not_found(string $route): void {
    global $__APP;
    http_response_code(404);
    $pretty = $__APP['public'] . '/html/errors/404.html';
    if (is_file($pretty)) { readfile($pretty); return; }
    header('Content-Type: text/plain; charset=utf-8');
    echo "404 Not Found: {$route}";
}

// ─────────────────────────────────────────────────────────────────────────────
// ROUTES
switch (true) {

    // Home → push to login (adjust if you want dashboard by default)
    case $method === 'GET' && ($route === '/' || $route === '/home'):
        header('Location: ' . (($__APP['base_path'] ?? '') . '/login'));
        exit;

    // ── AUTH ─────────────────────────────────────────────────────────────────
    case $method === 'GET' && $route === '/login':
        include_view('login.html');                  // public/html/login.html
        break;

    case $method === 'POST' && $route === '/auth/login':
        require_src('auth/login_process.php');       // handles POST auth
        break;

    case $method === 'GET' && $route === '/auth/logout':
        require_src('auth/logout.php');              // destroys session, etc.
        break;

    // ── DASHBOARD PAGES ──────────────────────────────────────────────────────
    case $method === 'GET' && $route === '/dashboard':
        // If dashboard.php renders HTML directly, include it:
        require_src('dashboard/dashboard.php');      // controller/view
        // If you prefer pure HTML view: include_view('dashboard_view.html');
        break;

    case $method === 'GET' && $route === '/sections/create':
        include_view('create_section.html');         // form page
        break;

    case $method === 'POST' && $route === '/sections/create':
        require_src('dashboard/create_section.php'); // create handler
        break;

    // Section detail (GET) – pattern /sections/{id}
    case $method === 'GET' && preg_match('#^/sections/(\d+)$#', $route, $m):
        // You can set $_GET['id'] so your existing PHP can read it
        $_GET['id'] = $m[1];
        // Either render server PHP:
        require_src('dashboard/section_detail.php');
        // Or show a static view instead:
        // include_view('section_detail.html');
        break;

    // Update section (POST) – /sections/{id}/update
    case $method === 'POST' && preg_match('#^/sections/(\d+)/update$#', $route, $m):
        $_POST['id'] = $m[1];
        require_src('dashboard/update_section.php');
        break;

    // Delete section (POST) – /sections/{id}/delete
    case $method === 'POST' && preg_match('#^/sections/(\d+)/delete$#', $route, $m):
        $_POST['id'] = $m[1];
        require_src('dashboard/delete_section.php');
        break;

    // Delete students in section (POST) – /sections/{id}/students/delete
    case $method === 'POST' && preg_match('#^/sections/(\d+)/students/delete$#', $route, $m):
        $_POST['section_id'] = $m[1];
        require_src('dashboard/delete_students.php');
        break;

    // Download CSV (GET) – /sections/{id}/download
    case $method === 'GET' && preg_match('#^/sections/(\d+)/download$#', $route, $m):
        $_GET['section_id'] = $m[1];
        require_src('dashboard/download_section_students_csv.php');
        break;

    // Student register/update (POST)
    case $method === 'POST' && $route === '/students/register':
        require_src('dashboard/student_register.php');
        break;

    case $method === 'POST' && $route === '/students/update':
        require_src('dashboard/update_student.php');
        break;

    // Batch upload (POST)
    case $method === 'POST' && $route === '/batch_upload':
        require_src('dashboard/batch_upload.php');
        break;

    // ── HEALTH CHECK ─────────────────────────────────────────────────────────
    case $method === 'GET' && $route === '/health':
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'env' => $__APP['env'] ?? 'unknown']);
        break;

    // 404 fallback
    default:
        not_found($route);
        break;
}
