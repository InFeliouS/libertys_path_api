<?php
// teachers_view.php
// Minimal, safe endpoint for listing/updating/deleting teachers.
// - GET?action=list      -> { ok:true, data:[...] }
// - POST?action=update   -> { ok:true } or { ok:false, error }
// - POST?action=delete   -> { ok:true } or { ok:false, error }

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();

// simple JSON helper
function j($arr, int $code = 200) {
    http_response_code($code);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

// require login
if (!isset($_SESSION['teacher_id'])) {
    j(['ok' => false, 'error' => 'Not logged in'], 401);
}

// require admin for mutating actions
$role = strtoupper((string)($_SESSION['role'] ?? ''));
$isAdmin = ($role === 'ADMIN');
// Compute DB path (adjust if your project layout differs)

$dbPaths = [
    __DIR__ . '/src/config/db.php',
    __DIR__ . '/../../src/config/db.php',
    __DIR__ . '/../config/db.php',
    __DIR__ . '/config/db.php',
];
$dbLoaded = false;
foreach ($dbPaths as $p) {
    if (is_file($p)) {
        require_once $p; // expects $pdo
        $dbLoaded = true;
        break;
    }
}
if (!isset($pdo) || !$pdo instanceof PDO) {
    error_log('teachers_view: db.php not found or $pdo missing');
    j(['ok' => false, 'error' => 'Server misconfiguration'], 500);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_REQUEST['action'] ?? ($method === 'GET' ? 'list' : '');

try {
    // ----------------------------
    // LIST (GET)
    // ----------------------------
    if ($method === 'GET' && $action === 'list') {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, username FROM teachers ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // normalize/null-handling
        $data = array_map(function($r){
            return [
                'id' => isset($r['id']) ? (int)$r['id'] : 0,
                'first_name' => $r['first_name'] ?? '',
                'last_name' => $r['last_name'] ?? '',
                'username' => $r['username'] ?? '',
            ];
        }, $rows ?: []);

        j(['ok' => true, 'data' => $data]);
    }

    // For POST actions below, ensure admin
    if (!$isAdmin) {
        j(['ok' => false, 'error' => 'Forbidden'], 403);
    }

    // ----------------------------
    // DELETE (POST)
    // ----------------------------
    if ($method === 'POST' && $action === 'delete') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            j(['ok' => false, 'error' => 'Invalid id'], 400);
        }

        // Prevent deleting yourself (use session teacher_id)
        $me = (int)($_SESSION['teacher_id'] ?? 0);
        if ($me === $id) {
            j(['ok' => false, 'error' => "Can't delete current user"], 400);
        }

        // delete
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // check affected rows (soft check)
        if ($stmt->rowCount() === 0) {
            j(['ok' => false, 'error' => 'No record deleted (id may not exist)'], 404);
        }

        j(['ok' => true]);
    }

    // ----------------------------
    // UPDATE (POST)
    // ----------------------------
    if ($method === 'POST' && $action === 'update') {
        // Accept standard form-data fields
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $username = trim((string)($_POST['username'] ?? ''));
        $first_name = trim((string)($_POST['first_name'] ?? ''));
        $last_name = trim((string)($_POST['last_name'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || $username === '') {
            j(['ok' => false, 'error' => 'Missing required fields'], 400);
        }

        // ensure username unique (exclude current id)
        $stmt = $pdo->prepare("SELECT id FROM teachers WHERE username = :u AND id <> :id LIMIT 1");
        $stmt->execute([':u' => $username, ':id' => $id]);
        if ($stmt->fetch()) {
            j(['ok' => false, 'error' => 'Username already taken'], 409);
        }

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE teachers SET username = :u, first_name = :fn, last_name = :ln, password = :pw WHERE id = :id");
            $stmt->execute([
                ':u' => $username,
                ':fn' => $first_name,
                ':ln' => $last_name,
                ':pw' => $hash,
                ':id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE teachers SET username = :u, first_name = :fn, last_name = :ln WHERE id = :id");
            $stmt->execute([
                ':u' => $username,
                ':fn' => $first_name,
                ':ln' => $last_name,
                ':id' => $id
            ]);
        }

        // success
        j(['ok' => true]);
    }

    // Unknown action
    j(['ok' => false, 'error' => 'Unknown action'], 400);

} catch (Throwable $e) {
    error_log('teachers_view error: ' . $e->getMessage());
    j(['ok' => false, 'error' => 'Server error'], 500);
}
