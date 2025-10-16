<?php
// public/api/v1/sections_available.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../../src/config/db.php';

try {
    // Return only sections that are NOT already assigned to ANY teacher
    // Optional when editing: ?include_for_teacher_id=123 keeps that teacher's current sections visible.
    $includeFor = isset($_GET['include_for_teacher_id']) ? (int)$_GET['include_for_teacher_id'] : 0;

    if ($includeFor > 0) {
        $sql = "
            SELECT s.id, s.section_name, s.start_school_year, s.end_school_year
            FROM sections s
            WHERE NOT EXISTS (
                SELECT 1 FROM teacher_sections ts
                WHERE ts.section_id = s.id AND ts.teacher_id <> :tid
            )
            ORDER BY s.section_name ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tid', $includeFor, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "
            SELECT s.id, s.section_name, s.start_school_year, s.end_school_year
            FROM sections s
            WHERE NOT EXISTS (
                SELECT 1 FROM teacher_sections ts
                WHERE ts.section_id = s.id
            )
            ORDER BY s.section_name ASC
        ";
        $stmt = $pdo->query($sql);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    echo json_encode(['success' => true, 'sections' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
