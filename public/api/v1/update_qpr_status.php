<?php
// public/api/v1/update_qpr_status.php

header("Content-Type: application/json");
session_start();

// 1) Read raw POST body and decode JSON
$raw   = file_get_contents("php://input");
$input = json_decode($raw, true);

// 1a) JSON parse error?
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON",
        "error"   => json_last_error_msg(),
        "raw"     => $raw
    ]);
    exit;
}

// 1b) Missing required fields?
if (!isset($input['student_id'], $input['qpr_field'], $input['status'])) {
    http_response_code(400);
    echo json_encode([
        "success"  => false,
        "message"  => "Missing required fields.",
        "received" => array_keys($input)
    ]);
    exit;
}

// 2) Extract values
$student_id = $input['student_id'];
$qpr_field  = $input['qpr_field'];
$new_status = $input['status'];

// 3) Load database connection
require_once __DIR__ . '/../../../src/config/db.php';

// 4) Define allowed status columns and derive retry columns
$allowed_status_fields = [
    'first_qpr_status',
    'second_qpr_status',
    'third_qpr_status',
    // …add each room’s status column here
];
$allowed_retry_fields = array_map(
    fn($f) => str_replace('_status', '_retries', $f),
    $allowed_status_fields
);

// 5) Handle retry‐count “increment” action
if ($new_status === 'increment') {
    // derive the actual retries column name
    $retry_col = str_replace('_status', '_retries', $qpr_field);

    if (!in_array($retry_col, $allowed_retry_fields, true)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid retries field: {$retry_col}"
        ]);
        exit;
    }

    $sql  = "UPDATE student_progress
             SET `$retry_col` = `$retry_col` + 1
             WHERE student_id = :id";
    $stmt = $pdo->prepare($sql);
    $ok   = $stmt->execute(['id' => $student_id]);

    echo json_encode([
        "success" => (bool)$ok,
        "action"  => "increment",
        "field"   => $retry_col
    ]);
    exit;
}

// 6) Validate requested status
$allowed_status = ['unvisited', 'visited', 'complete'];
if (!in_array($new_status, $allowed_status, true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid status."]);
    exit;
}

// 7) Validate requested status column
if (!in_array($qpr_field, $allowed_status_fields, true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid field."]);
    exit;
}

// 8) Prevent overwriting “complete”
$checkSql  = "SELECT `$qpr_field` FROM student_progress WHERE student_id = :id";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute(['id' => $student_id]);
$current = $checkStmt->fetchColumn();

if ($current === 'complete') {
    echo json_encode(["success" => true, "message" => "Already completed."]);
    exit;
}

// 9) Perform the status update
$updSql  = "UPDATE student_progress
            SET `$qpr_field` = :status
            WHERE student_id = :id";
$updStmt = $pdo->prepare($updSql);
$ok      = $updStmt->execute([
    'status' => $new_status,
    'id'     => $student_id
]);

echo json_encode(["success" => (bool)$ok]);
