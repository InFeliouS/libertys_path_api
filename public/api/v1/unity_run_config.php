<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try {
    require_once __DIR__ . '/../../../src/config/db.php'; // expects $pdo

    $input = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

    $section_id = isset($input['section_id']) ? intval($input['section_id']) : 0;
    $section    = isset($input['section']) ? trim((string)$input['section']) : '';
    $teacher_id = isset($input['teacher_id']) ? intval($input['teacher_id']) : 0;

    if ($section_id > 0) {
        $stmt = $pdo->prepare("
            SELECT tc.room_count, tc.enemy_count
            FROM teacher_sections ts
            JOIN teacher_configs tc ON tc.teacher_id = ts.teacher_id
            WHERE ts.section_id = :sid
            LIMIT 1
        ");
        $stmt->execute([':sid' => $section_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($section !== '') {
        // find section id by name, then join to teacher_configs
        $stmt = $pdo->prepare("
            SELECT tc.room_count, tc.enemy_count
            FROM sections s
            JOIN teacher_sections ts ON ts.section_id = s.id
            JOIN teacher_configs tc ON tc.teacher_id = ts.teacher_id
            WHERE UPPER(TRIM(s.section_name)) = UPPER(TRIM(:sname))
            LIMIT 1
        ");
        $stmt->execute([':sname' => $section]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($teacher_id > 0) {
        $stmt = $pdo->prepare("SELECT room_count, enemy_count FROM teacher_configs WHERE teacher_id = :tid LIMIT 1");
        $stmt->execute([':tid' => $teacher_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Provide section_id OR section OR teacher_id']);
        exit;
    }

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'No config found']);
        exit;
    }

    $room = max(1, min(8, (int)$row['room_count']));
    $enemy = max(1, min(8, (int)$row['enemy_count']));

    echo json_encode(['success' => true, 'room_count' => $room, 'enemy_count' => $enemy]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
