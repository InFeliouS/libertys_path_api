<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/src/auth/auth_guard.php';
require_once $ROOT . '/src/config/db.php';
start_session_once();
require_auth();

// ... (rest of file unchanged)


if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405); header('Allow: POST');
    header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Use POST']); exit;
}

$id   = (int)($_POST['id'] ?? 0);
$meId = (int)($_SESSION['teacher_id'] ?? 0);
$role = $_SESSION['role'] ?? 'TEACHER';
if ($id <= 0) { http_response_code(400); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }

try {
    /** @var PDO $pdo */
    $s = $pdo->prepare("SELECT created_by FROM guard_questions WHERE id = ?");
    $s->execute([$id]);
    $ownerId = (int)$s->fetchColumn();
    if ($ownerId <= 0) { http_response_code(404); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Question not found']); exit; }

    if ($role !== 'ADMIN' && $ownerId !== $meId) {
        http_response_code(403); header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Forbidden']); exit;
    }

    $fields = ['question_text','choice1','choice2','choice3','choice4','correct_index'];
    $set = []; $vals = [];
    foreach ($fields as $f) {
        if (array_key_exists($f, $_POST)) { $set[] = "$f = ?"; $vals[] = $_POST[$f]; }
    }
    if (!$set) { header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit; }

    $vals[] = $id;
    $sql = "UPDATE guard_questions SET ".implode(', ', $set)." WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($vals);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok'=>true,'id'=>$id]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false,'error'=>'Update failed','detail'=>$e->getMessage()]);
}
