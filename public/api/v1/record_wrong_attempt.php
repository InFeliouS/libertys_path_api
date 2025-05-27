<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../src/config/db.php';

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$student_id = $input['student_id'] ?? null;
$question_id = $input['question_id'] ?? null;
$increment = intval($input['increment'] ?? 1);

// Validate input
if (!$student_id || !$question_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Map question IDs to database column names
$column_map = [
    "Question 1" => "q1_attempts",
    "Question 2" => "q2_attempts",
    "Question 3" => "q3_attempts",
    "Question 4" => "q4_attempts",
    "Question 5" => "q5_attempts",
    "Question 6" => "q6_attempts"
];

if (!isset($column_map[$question_id])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid question ID']);
    exit;
}

$column = $column_map[$question_id];

try {
    // Try to increment the counter
    $stmt = $pdo->prepare("UPDATE first_qpr_attempts SET $column = $column + ? WHERE student_id = ?");
    $stmt->execute([$increment, $student_id]);

    if ($stmt->rowCount() === 0) {
        // Row might not exist yet; insert it and then update
        $pdo->prepare("INSERT IGNORE INTO first_qpr_attempts (student_id) VALUES (?)")->execute([$student_id]);
        $pdo->prepare("UPDATE first_qpr_attempts SET $column = $column + ? WHERE student_id = ?")->execute([$increment, $student_id]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
