<?php
// src/dashboard/teachers_delete.php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Not logged in']); exit;
}
if (!function_exists('is_admin') || !is_admin()) {
    http_response_code(403); echo json_encode(['success'=>false,'error'=>'Admins only']); exit;
}

$in = json_decode(file_get_contents('php://input'), true);
if (empty($in['teacher_id'])) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing teacher_id']); exit; }
$tid = (int)$in['teacher_id'];

require_once __DIR__ . '/../config/db.php';
try {
    // safety: don't allow deleting the admin account or yourself (optional)
    $stmt = $pdo->prepare("SELECT role FROM teachers WHERE id = :id");
    $stmt->execute([':id'=>$tid]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r) { http_response_code(404); echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
    if ($r['role'] === 'ADMIN') { http_response_code(403); echo json_encode(['success'=>false,'error'=>'Cannot delete admin']); exit; }

    $del = $pdo->prepare("DELETE FROM teachers WHERE id = :id");
    $del->execute([':id'=>$tid]);
    echo json_encode(['success'=>true,'deleted'=>$tid]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
