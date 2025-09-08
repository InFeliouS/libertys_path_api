<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ct = $_SERVER['CONTENT_TYPE'] ?? '';
$data = (stripos($ct, 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST;

$section_id         = (int)($data['section_id'] ?? $data['id'] ?? 0);
$section_name       = trim((string)($data['section_name'] ?? ''));
$start_school_year  = trim((string)($data['start_school_year'] ?? ''));
$end_school_year    = trim((string)($data['end_school_year'] ?? ''));

if ($section_id <= 0 || $section_name === '' || $start_school_year === '' || $end_school_year === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing fields']); exit;
}

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

$stmt = $mysqli->prepare("UPDATE sections SET section_name=?, start_school_year=?, end_school_year=? WHERE id=?");
$stmt->bind_param("sssi", $section_name, $start_school_year, $end_school_year, $section_id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) { http_response_code(500); echo json_encode(['success'=>false,'error'=>$mysqli->error]); exit; }

echo json_encode(['success'=>true]);
