<?php
// teachers_view.php
// Endpoint for AJAX list / update / delete for teachers
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

// load DB (try common paths)
$dbPaths = [
    __DIR__ . '/src/config/db.php',
    __DIR__ . '/../src/config/db.php',
    __DIR__ . '/../config/db.php',
    __DIR__ . '/config/db.php',
];
foreach ($dbPaths as $p) {
    if (file_exists($p)) {
        require_once $p;
        break;
    }
}
if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database not configured.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? 'list';

// helper to escape for HTML rendering when server sends HTML (not used for JSON payload)
function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

try {
    if ($method === 'GET' && $action === 'list') {
        // Return list of teachers
        $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, role FROM teachers ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['ok' => true, 'data' => $rows]);
        exit;
    }

    if ($method === 'POST' && $action === 'delete') {
        // Delete teacher by id (admin-only ideally)
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Invalid id']);
            exit;
        }
        // Prevent deleting yourself (basic guard)
        if (isset($_SESSION['id']) && (int)$_SESSION['id'] === $id) {
            echo json_encode(['ok' => false, 'error' => "Can't delete current user"]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($method === 'POST' && $action === 'update') {
        // Update teacher (username, first_name, last_name, optionally password)
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $username = trim($_POST['username'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || $username === '') {
            echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
            exit;
        }

        // Basic username unique check (exclude current id)
        $stmt = $pdo->prepare("SELECT id FROM teachers WHERE username = :u AND id <> :id");
        $stmt->execute([':u' => $username, ':id' => $id]);
        if ($stmt->fetch()) {
            echo json_encode(['ok' => false, 'error' => 'Username already taken']);
            exit;
        }

        if ($password !== '') {
            // hash
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE teachers SET username = :u, first_name = :fn, last_name = :ln, password = :pw WHERE id = :id");
            $stmt->execute([':u' => $username, ':fn' => $first_name, ':ln' => $last_name, ':pw' => $hash, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE teachers SET username = :u, first_name = :fn, last_name = :ln WHERE id = :id");
            $stmt->execute([':u' => $username, ':fn' => $first_name, ':ln' => $last_name, ':id' => $id]);
        }

        echo json_encode(['ok' => true]);
        exit;
    }

    // if we get here: unknown action
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    exit;

} catch (Throwable $e) {
    error_log("teachers_view endpoint error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Server error']);
    exit;
}
