<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/src/config/db.php';

header('Content-Type: application/json; charset=utf-8');

// ... (rest unchanged)


$section_id = (int)($_GET['section_id'] ?? $_POST['section_id'] ?? 0);
if ($section_id <= 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Missing section_id']); exit;
}

try {
    /** @var PDO $pdo */
    // Find teacher(s) assigned to this section
    $t = $pdo->prepare("SELECT teacher_id FROM teacher_sections WHERE section_id = ?");
    $t->execute([$section_id]);
    $teacherIds = array_map('intval', $t->fetchAll(PDO::FETCH_COLUMN));

    if (!$teacherIds) {
        echo json_encode(['ok'=>true, 'section_id'=>$section_id, 'items'=>[]]); exit;
    }

    $placeholders = implode(',', array_fill(0, count($teacherIds), '?'));
    $sql = "
        SELECT id, question_text, choice1, choice2, choice3, choice4, correct_index
        FROM guard_questions
        WHERE created_by IN ($placeholders)
        ORDER BY id ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($teacherIds);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    echo json_encode(['ok'=>true, 'section_id'=>$section_id, 'items'=>$rows], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'DB error','detail'=>$e->getMessage()]);
}
