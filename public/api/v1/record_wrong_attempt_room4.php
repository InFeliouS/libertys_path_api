<?php
// public/api/v1/record_wrong_attempt_room4.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../src/config/db.php';

// Decode incoming JSON
$in = json_decode(file_get_contents('php://input'), true);
$student_id  = $in['student_id']            ?? '';
$question_id = isset($in['question_id'])   ? (int)$in['question_id'] : 0;
$increment   = isset($in['increment'])     ? (int)$in['increment']   : 1;

// Validate inputs
if (
    !$student_id ||
    $question_id < 1    || $question_id > 6 ||
    $increment < 1
) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'error'   => 'Invalid student_id, question_id or increment'
    ]);
    exit;
}

// Build the column name dynamically
$col = "question{$question_id}";

try {
    // 1) Try updating an existing row
    $sql = "
      UPDATE `fourth_qpr_attempts`
         SET `$col` = `$col` + :inc,
             `updated_at` = CURRENT_TIMESTAMP
       WHERE `student_id` = :sid
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':inc' => $increment,
      ':sid' => $student_id
    ]);

    // 2) If no row was touched, insert then retry
    if ($stmt->rowCount() === 0) {
        // Insert a fresh row (all zeros by default)
        $pdo->prepare("
          INSERT INTO `fourth_qpr_attempts` (student_id)
               VALUES (:sid)
          ON DUPLICATE KEY UPDATE student_id = student_id
        ")->execute([':sid' => $student_id]);

        // Retry the update
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          ':inc' => $increment,
          ':sid' => $student_id
        ]);
    }

    // Success response
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'error'   => $e->getMessage()
    ]);
}
