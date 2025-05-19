<?php
// src/dashboard/update_student.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    exit(json_encode(['success'=>false,'error'=>'Not authenticated']));
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success'=>false,'error'=>'Must POST']));
}

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$raw = json_decode(file_get_contents('php://input'), true);
$sid        = intval($raw['student_id']   ?? 0);
$last       = strtoupper(trim($raw['last_name']   ?? ''));
$given      = strtoupper(trim($raw['given_name']  ?? ''));
$middle     = strtoupper(trim($raw['middle_name'] ?? ''));
$birth_sex  = $raw['birth_sex'] ?? '';

if (!$sid || !$last || !$given || !in_array($birth_sex, ['Male','Female'], true)) {
    http_response_code(400);
    exit(json_encode(['success'=>false,'error'=>'Invalid input']));
}

try {
    $stmt = $pdo->prepare("
      UPDATE students
         SET last_name   = ?,
             given_name  = ?,
             middle_name = ?,
             birth_sex   = ?
       WHERE id = ?
    ");
    $stmt->execute([
      $last,
      $given,
      $middle ?: null,
      $birth_sex,
      $sid
    ]);

    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
