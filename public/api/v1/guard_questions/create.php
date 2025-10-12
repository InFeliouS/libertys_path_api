<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/src/auth/auth_guard.php';
require_once $ROOT . '/src/config/db.php';
start_session_once();
require_auth();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405); header('Allow: POST');
    header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Use POST']); exit;
}

$role  = $_SESSION['role'] ?? 'TEACHER';
$meId  = (int)($_SESSION['teacher_id'] ?? 0);

// Admin can create for another teacher; teachers create for themselves.
$ownerId = (int)($_POST['teacher_id'] ?? $_GET['teacher_id'] ?? 0);
if ($role !== 'ADMIN' || $ownerId <= 0) { $ownerId = $meId; }
if ($ownerId <= 0) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'error'=>'Missing teacher context']); exit;
}

$question     = trim((string)($_POST['question_text'] ?? ''));
$c1           = trim((string)($_POST['choice1'] ?? ''));
$c2           = trim((string)($_POST['choice2'] ?? ''));
$c3           = trim((string)($_POST['choice3'] ?? ''));
$c4           = trim((string)($_POST['choice4'] ?? ''));
$correctIndex = (int)($_POST['correct_index'] ?? -1);

if ($question === '' || $c1 === '' || $c2 === '' || $c3 === '' || $c4 === '' || $correctIndex < 0 || $correctIndex > 3) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'error'=>'Invalid fields']); exit;
}

try {
    /** @var PDO $pdo */
    $stmt = $pdo->prepare("
        INSERT INTO guard_questions
          (question_text, choice1, choice2, choice3, choice4, correct_index, created_by)
        VALUES
          (:q, :c1, :c2, :c3, :c4, :ci, :owner)
    ");
    $stmt->execute([
        ':q'     => $question,
        ':c1'    => $c1,
        ':c2'    => $c2,
        ':c3'    => $c3,
        ':c4'    => $c4,
        ':ci'    => $correctIndex,
        ':owner' => $ownerId,
    ]);
    $id = (int)$pdo->lastInsertId();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>true,'id'=>$id]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'error'=>'Create failed','detail'=>$e->getMessage()]);
}
