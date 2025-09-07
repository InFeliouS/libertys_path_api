<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ct = $_SERVER['CONTENT_TYPE'] ?? '';
$body = (stripos($ct, 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST;

$section_name       = trim((string)($body['section_name'] ?? ''));
$start_school_year  = trim((string)($body['start_school_year'] ?? ''));
$end_school_year    = trim((string)($body['end_school_year'] ?? ''));

if ($section_name === '' || $start_school_year === '' || $end_school_year === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing fields']); exit;
}

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

$stmt = $mysqli->prepare("INSERT INTO sections (section_name, start_school_year, end_school_year) VALUES (?,?,?)");
$stmt->bind_param("sss", $section_name, $start_school_year, $end_school_year);
$ok = $stmt->execute();
$newId = $ok ? $stmt->insert_id : null;
$stmt->close();

if (!$ok) { http_response_code(500); echo json_encode(['success'=>false,'error'=>$mysqli->error]); exit; }

echo json_encode(['success'=>true,'id'=>(int)$newId]);
