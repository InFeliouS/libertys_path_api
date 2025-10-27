<?php
// public/api/v1/sections.php
// Admins: returns all sections with assigned teacher name.
// Teachers: returns ONLY the sections assigned to the logged-in teacher.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    // --- Session for role/id
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Resolve role (ADMIN vs TEACHER) using helpers or session
    $sessionRole = strtoupper($_SESSION['role'] ?? '');
    $isAdmin = function_exists('is_admin') ? is_admin() : ($sessionRole === 'ADMIN');

    // Resolve current teacher/user id from common keys
    $currentUserId = null;
    foreach (['teacher_id','user_id','id'] as $k) {
        if (isset($_SESSION[$k]) && is_numeric($_SESSION[$k])) {
            $currentUserId = (int) $_SESSION[$k];
            break;
        }
    }

    // --- Include DB (adjust the first existing path if needed)
    $paths = [
        __DIR__ . '/../../config/db.php',
        __DIR__ . '/../../db.php',
        __DIR__ . '/../../../src/config/db.php',
        __DIR__ . '/../../../db.php',
    ];
    $found = false;
    foreach ($paths as $p) {
        if (is_file($p)) { require_once $p; $found = true; break; }
    }
    if (!$found) {
        throw new RuntimeException('db.php not found — update include path in sections.php');
    }

    // --- Get PDO
    if (!isset($pdo) || !$pdo) {
        if (isset($conn) && $conn instanceof mysqli) {
            $hostParts = explode(':', $conn->host_info ?? 'localhost');
            $host = $hostParts[0] ?: 'localhost';
            $dbNameRow = $conn->query("SELECT DATABASE()")->fetch_row();
            $dbName = $dbNameRow ? $dbNameRow[0] : '';
            $user = $conn->user ?? 'root';
            $pass = $conn->passwd ?? '';
            $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } elseif (function_exists('get_pdo')) {
            $pdo = get_pdo();
        } else {
            throw new RuntimeException('No PDO connection available.');
        }
    }

    // --- Base SQL (latest mapping per section)
    $baseSql = "
        SELECT
            s.*,
            t.first_name AS teacher_first_name,
            t.last_name  AS teacher_last_name,
            t.username   AS teacher_username,
            ts.teacher_id AS assigned_teacher_id
        FROM sections s
        LEFT JOIN (
            SELECT ts.section_id, ts.teacher_id
            FROM teacher_sections ts
            INNER JOIN (
                SELECT section_id, MAX(id) AS max_id
                FROM teacher_sections
                GROUP BY section_id
            ) pick ON pick.section_id = ts.section_id AND pick.max_id = ts.id
            -- If you track active flags, enable this:
            -- WHERE COALESCE(ts.is_active, 1) = 1
        ) ts ON ts.section_id = s.id
        LEFT JOIN teachers t ON t.id = ts.teacher_id
    ";

    // --- Role-based filtering:
    // Admin  -> no WHERE (see everything)
    // Teacher-> only rows where latest mapping belongs to the logged-in teacher
    $params = [];
    if (!$isAdmin) {
        if (!$currentUserId) {
            // No id in session => return empty to be safe
            echo json_encode(['success' => true, 'sections' => []], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $baseSql .= " WHERE ts.teacher_id = :tid ";
        $params[':tid'] = $currentUserId;
    }

    // Order consistently
    $baseSql .= " ORDER BY s.id ASC ";

    // --- Execute
    $stmt = $pdo->prepare($baseSql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll() ?: [];

    // --- Add combined teacher_name (optional helper)
    foreach ($rows as &$r) {
        $first = isset($r['teacher_first_name']) ? trim((string)$r['teacher_first_name']) : '';
        $last  = isset($r['teacher_last_name'])  ? trim((string)$r['teacher_last_name'])  : '';
        $combo = trim($first . ' ' . $last);
        if ($combo === '' && !empty($r['teacher_username'])) {
            $combo = $r['teacher_username'];
        }
        $r['teacher_name'] = $combo;
    }
    unset($r);

    echo json_encode([
        'success'  => true,
        'sections' => $rows,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
