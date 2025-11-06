<?php
// src/dashboard/teachers_update.php
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE);

header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

// Require login (only admins allowed to update teacher accounts)
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// read raw JSON body
$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$teacherId = isset($in['teacher_id']) ? (int)$in['teacher_id'] : 0;
$username  = isset($in['username']) ? trim((string)$in['username']) : '';
$first     = isset($in['first_name']) ? trim((string)$in['first_name']) : '';
$last      = isset($in['last_name']) ? trim((string)$in['last_name']) : '';
$password  = isset($in['password']) ? (string)$in['password'] : null;

if ($teacherId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing teacher_id']);
    exit;
}
if ($username === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Username is required']);
    exit;
}

// basic username validation (adjust rules as needed)
if (!preg_match('/^[A-Za-z0-9_\.@-]{3,50}$/', $username)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid username']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    // Start transaction to keep updates consistent
    $pdo->beginTransaction();

    // 1) check teacher exists
    $stmt = $pdo->prepare("SELECT id, username FROM teachers WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $teacherId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$existing) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Teacher not found']);
        exit;
    }

    // 2) check username uniqueness (if changed)
    if (strcasecmp($existing['username'], $username) !== 0) {
        $chk = $pdo->prepare("SELECT id FROM teachers WHERE username = :username LIMIT 1");
        $chk->execute([':username' => $username]);
        $conf = $chk->fetch(PDO::FETCH_ASSOC);
        if ($conf) {
            $pdo->rollBack();
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Username already exists']);
            exit;
        }
    }

    // 3) build update statement dynamically (only fields we want)
    $fields = [
        'username' => $username,
        'first_name' => $first,
        'last_name' => $last
    ];
    $sqlParts = [];
    $params = [];
    foreach ($fields as $k => $v) {
        $sqlParts[] = "$k = :$k";
        $params[":$k"] = $v;
    }

    // password only if provided (non-empty)
    if (is_string($password) && $password !== '') {
        // hash password securely
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sqlParts[] = "password = :password";
        $params[':password'] = $hash;
    }

    $params[':id'] = $teacherId;
    $sql = "UPDATE teachers SET " . implode(", ", $sqlParts) . " WHERE id = :id";
    $upd = $pdo->prepare($sql);
    $upd->execute($params);

    // 4) fetch updated teacher to return
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, role FROM teachers WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $teacherId]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    echo json_encode(['success' => true, 'teacher' => $teacher], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
