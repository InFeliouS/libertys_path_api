<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../src/config/db.php'; // provides $pdo (PDO)

try {
    $stmt = $pdo->query("SELECT * FROM guard_questions ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB error']);
}
