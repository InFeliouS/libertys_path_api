<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../src/config/db.php';

$input       = json_decode(file_get_contents('php://input'), true);
$student_id  = $input['student_id']  ?? null;
$question_id = $input['question_id'] ?? null;
$increment   = intval($input['increment'] ?? 1);

if (!$student_id || !$question_id) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Missing parameters']);
    exit;
}

// Map cardID → column
$validColumns = [
  "1"=>"bench1_attempts",
  "2"=>"bench2_attempts",
  "3"=>"bench3_attempts",
  "4"=>"bench4_attempts",
  "5"=>"bench5_attempts",
  "6"=>"bench6_attempts"
];

if (!isset($validColumns[$question_id])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Invalid question ID']);
    exit;
}

$column = $validColumns[$question_id];

try {
    $stmt = $pdo->prepare("UPDATE second_qpr_attempts SET $column = $column + ? WHERE student_id = ?");
    $stmt->execute([$increment, $student_id]);
    if ($stmt->rowCount()===0) {
        $pdo->prepare("INSERT IGNORE INTO second_qpr_attempts (student_id) VALUES (?)")
            ->execute([$student_id]);
        $pdo->prepare("UPDATE second_qpr_attempts SET $column = $column + ? WHERE student_id = ?")
            ->execute([$increment, $student_id]);
    }
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
