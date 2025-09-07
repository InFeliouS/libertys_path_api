<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ct = $_SERVER['CONTENT_TYPE'] ?? '';
$data = (stripos($ct, 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST;

$section_id = (int)($data['section_id'] ?? $data['id'] ?? 0);
if ($section_id <= 0) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Missing section_id']); exit; }

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

$mysqli->begin_transaction();

try {
    // delete students in section
    $stmt = $mysqli->prepare("DELETE FROM students WHERE section_id=?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();

    // delete section
    $stmt = $mysqli->prepare("DELETE FROM sections WHERE id=?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();

    $mysqli->commit();
    echo json_encode(['success'=>true]);
} catch (Throwable $e) {
    $mysqli->rollback();
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
