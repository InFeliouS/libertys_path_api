<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ct   = $_SERVER['CONTENT_TYPE'] ?? '';
$data = (stripos($ct, 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST;
$data = is_array($data) ? $data : [];

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

/* Delete by section */
if (isset($data['section_id'])) {
    $section_id = (int)$data['section_id'];
    if ($section_id <= 0) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Invalid section_id']); exit; }

    $stmt = $mysqli->prepare("DELETE FROM students WHERE section_id=?");
    $stmt->bind_param("i", $section_id);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) { http_response_code(500); echo json_encode(['success'=>false,'error'=>$mysqli->error]); exit; }
    echo json_encode(['success'=>true,'deleted'=>'by_section']); exit;
}

/* Delete by list of IDs */
$ids = $data['student_ids'] ?? [];
if (!is_array($ids) || !$ids) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Missing student_ids']); exit; }
$ids = array_values(array_filter(array_map('intval', $ids), fn($v)=>$v>0));
if (!$ids) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'No valid ids']); exit; }

$in    = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));
$stmt  = $mysqli->prepare("DELETE FROM students WHERE id IN ($in)");
$stmt->bind_param($types, ...$ids);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) { http_response_code(500); echo json_encode(['success'=>false,'error'=>$mysqli->error]); exit; }
echo json_encode(['success'=>true,'deleted'=>count($ids)]);
