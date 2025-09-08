<?php
// src/dashboard/delete_students.php

error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['student_ids']) || !is_array($input['student_ids'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $pdo->beginTransaction();
    $ids = array_map('intval', $input['student_ids']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // delete from student_progress
    $stmt = $pdo->prepare("DELETE FROM student_progress WHERE student_id IN ($placeholders)");
    $stmt->execute($ids);

    // delete from student_accounts
    $stmt = $pdo->prepare("DELETE FROM student_accounts WHERE student_id IN ($placeholders)");
    $stmt->execute($ids);

    // delete from students
    $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
    $stmt->execute($ids);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
