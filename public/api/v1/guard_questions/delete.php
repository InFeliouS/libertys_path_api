<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../src/config/db.php';

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if ($raw) $input = json_decode($raw, true) ?? [];
}

$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM guard_questions WHERE id=:id");
    $ok = $stmt->execute([':id' => $id]);
    echo json_encode(['success'=>$ok]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'DB error']);
}
