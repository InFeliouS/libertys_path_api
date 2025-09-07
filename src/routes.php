<?php
// Guard
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

/** Build a link that ALWAYS goes through the front controller */
function route_to(string $path): string {
    return base_url() . 'index.php?route=' . ltrim($path, '/');
}

/** Include an HTML view from /public/html and inject <base href> */
function include_view(string $relPath): void {
    global $__APP;

    $full = ($__APP['public'] ?? __DIR__) . '/html/' . ltrim($relPath, '/');
    if (!is_file($full)) {
        http_response_code(404);
        echo "View not found: html/{$relPath}";
        return;
    }

    $html = file_get_contents($full);
    if ($html === false) {
        http_response_code(500);
        echo "Failed to read view: html/{$relPath}";
        return;
    }

    $baseHref = htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8');
    if (preg_match('/<head[^>]*>/i', $html)) {
        $html = preg_replace('/(<head[^>]*>)/i', '$1' . "\n" . '<base href="' . $baseHref . '">', $html, 1);
    } else {
        $html = '<base href="' . $baseHref . '">' . "\n" . $html;
    }

    header('Content-Type: text/html; charset=utf-8');
    echo $html;
}

/** Require a PHP script from /src */
function require_src(string $relPath): void {
    global $__APP;
    $full = ($__APP['base'] ?? __DIR__) . '/src/' . ltrim($relPath, '/');
    if (!is_file($full)) {
        http_response_code(500);
        echo "Server file missing: src/{$relPath}";
        return;
    }
    require $full;
}

/** 404 fallback */
function not_found(string $r): void {
    global $__APP;
    http_response_code(404);
    $pretty = ($__APP['public'] ?? __DIR__) . '/html/errors/404.html';
    if (is_file($pretty)) { readfile($pretty); return; }
    header('Content-Type: text/plain; charset=utf-8');
    echo "404 Not Found: {$r}";
}

/** If request has JSON body, decode and merge into $_POST */
function hydrate_post_from_json(): void {
    $rm = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($rm !== 'POST' && $rm !== 'PUT' && $rm !== 'PATCH') return;
    $ct = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    if (stripos($ct, 'application/json') === false) return;
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) return;
    $data = json_decode($raw, true);
    if (is_array($data)) { $_POST = $data + $_POST; }
}
hydrate_post_from_json();

/* ───────────── Routes ───────────── */

switch (true) {
    // Home → Login
    case $method === 'GET' && ($route === '/' || $route === '/home'):
        header('Location: ' . route_to('login')); exit;

    // Ignore favicon
    case $method === 'GET' && $route === '/favicon.ico':
        http_response_code(204); exit;

    /* Auth */
    case $method === 'GET' && $route === '/login':
        include_view('login.html'); break;

    case $method === 'POST' && $route === '/login/process':
    case $method === 'POST' && $route === '/auth/login':
        require_src('auth/login_process.php'); break;

    case $method === 'GET' && ($route === '/auth/logout' || $route === '/logout'):
        require_src('auth/logout.php'); break;

    /* Dashboard */
    case $method === 'GET' && $route === '/dashboard':
        require_src('dashboard/dashboard.php'); break;

    /* Sections & forms */
    case $method === 'GET' && $route === '/sections/create':
        include_view('create_section.html'); break;

    case $method === 'POST' && $route === '/sections/create':
        require_src('dashboard/create_section.php'); break;

    // /sections/{id}
    case $method === 'GET' && preg_match('#^/sections/(\d+)$#', $route, $m):
        $_GET['id'] = $m[1]; require_src('dashboard/section_detail.php'); break;

    // /sections/{id}/update
    case $method === 'POST' && preg_match('#^/sections/(\d+)/update$#', $route, $m):
        $_POST['id'] = $m[1]; require_src('dashboard/update_section.php'); break;

    // /sections/{id}/delete
    case $method === 'POST' && preg_match('#^/sections/(\d+)/delete$#', $route, $m):
        $_POST['id'] = $m[1]; require_src('dashboard/delete_section.php'); break;

    // Aliases the JS might use (JSON body carries section_id)
    case $method === 'POST' && $route === '/sections/update':
        require_src('dashboard/update_section.php'); break;

    case $method === 'POST' && $route === '/sections/deleteSection':
        require_src('dashboard/delete_section.php'); break;

    // /sections/view?section_id=123
    case $method === 'GET' && $route === '/sections/view':
        if (isset($_GET['section_id'])) $_GET['id'] = $_GET['section_id'];
        require_src('dashboard/section_detail.php'); break;

    /* Student Register page */
    case $method === 'GET' && $route === '/register':
        include_view('register_student.html'); break;

    /* Students */
    case $method === 'POST' && $route === '/students/register':
        require_src('dashboard/student_register.php'); break;

    case $method === 'POST' && $route === '/students/update':
        require_src('dashboard/update_student.php'); break;

    /* Batch upload */
    case $method === 'POST' && $route === '/batch_upload':
        require_src('dashboard/batch_upload.php'); break;

    /* Health */
    case $method === 'GET' && $route === '/health':
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'env' => $__APP['env'] ?? 'unknown']); break;

    /* Fallback */
    default: not_found($route); break;
}
