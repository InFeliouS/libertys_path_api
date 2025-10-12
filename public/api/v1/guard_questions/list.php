<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/src/auth/auth_guard.php';
require_once $ROOT . '/src/config/db.php';
start_session_once();
require_auth();

header('Content-Type: application/json; charset=utf-8');

$meId = (int)($_SESSION['teacher_id'] ?? 0);
$role = $_SESSION['role'] ?? 'TEACHER';
$filterTeacherId = (int)($_GET['teacher_id'] ?? 0);
if ($role !== 'ADMIN') { $filterTeacherId = $meId; }

try {
    /** @var PDO $pdo */
    if ($filterTeacherId > 0) {
        $stmt = $pdo->prepare("
            SELECT id, question_text, choice1, choice2, choice3, choice4, correct_index,
                   created_by, created_at
            FROM guard_questions
            WHERE created_by = ?
            ORDER BY id DESC
        ");
        $stmt->execute([$filterTeacherId]);
    } else {
        $stmt = $pdo->query("
            SELECT id, question_text, choice1, choice2, choice3, choice4, correct_index,
                   created_by, created_at
            FROM guard_questions
            ORDER BY id DESC
        ");
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    echo json_encode(['ok' => true, 'items' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'DB error', 'detail' => $e->getMessage()]);
}
