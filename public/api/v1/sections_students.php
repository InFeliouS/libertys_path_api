<?php
/**
 * List students for a section with username.
 * Uses:
 *   - students(id, given_name, middle_name, last_name, section_id, ...)
 *   - student_accounts(student_id, username, ...)
 *
 * GET ?section_id=10
 */
declare(strict_types=1);
header('Content-Type: application/json');

try {
    $sid = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
    if ($sid <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Missing or invalid section_id']);
        exit;
    }

    require_once __DIR__ . '/../../../src/config/db.php'; // $pdo

    $sql = "
        SELECT
            s.id,
            s.last_name,
            s.given_name,
            s.middle_name,
            a.username
        FROM students s
        LEFT JOIN student_accounts a ON a.student_id = s.id
        WHERE s.section_id = :sid
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':sid' => $sid]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Sort in PHP to avoid ORDER BY on unknown columns
    usort($rows, static function($a, $b) {
        $cmp = strcasecmp($a['last_name'] ?? '', $b['last_name'] ?? '');
        if ($cmp !== 0) return $cmp;
        $cmp = strcasecmp($a['given_name'] ?? '', $b['given_name'] ?? '');
        if ($cmp !== 0) return $cmp;
        return strcasecmp($a['middle_name'] ?? '', $b['middle_name'] ?? '');
    });

    echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
