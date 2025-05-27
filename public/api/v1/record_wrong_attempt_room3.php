<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../src/config/db.php';

// Decode JSON payload
$in = json_decode(file_get_contents('php://input'), true);
$student_id  = $in['student_id']  ?? '';
$question_id = intval($in['question_id'] ?? 0);
$increment   = intval($in['increment']   ?? 1);

// Validate
if (!$student_id || $question_id < 1 || $question_id > 6) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Invalid student_id or question_id']);
    exit;
}

// Build dynamic column name: question1..question6
$col = "question{$question_id}";

try {
    // 1) Try updating an existing row
    $sql = "
      UPDATE `third_qpr_attempts`
         SET `$col` = `$col` + :inc,
             `updated_at` = CURRENT_TIMESTAMP
       WHERE `student_id` = :sid
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':inc'=>$increment, ':sid'=>$student_id]);

    // 2) If no row was updated, insert a new one (defaults to 0) then retry
    if ($stmt->rowCount() === 0) {
        $pdo->prepare(
          "INSERT INTO `third_qpr_attempts` (student_id)
             VALUES (:sid)
           ON DUPLICATE KEY UPDATE student_id = student_id"
        )->execute([':sid'=>$student_id]);

        // Retry the increment
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':inc'=>$increment, ':sid'=>$student_id]);
    }

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
