<?php
// File: public/api/v1/unity_run_difficulty.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try {
    require_once __DIR__ . '/../../../src/config/db.php'; // expects $pdo (PDO)

    $input = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

    $section_id = isset($input['section_id']) ? intval($input['section_id']) : 0;
    $section    = isset($input['section']) ? trim((string)$input['section']) : '';
    $teacher_id = isset($input['teacher_id']) ? intval($input['teacher_id']) : 0;

    $row = false;

    if ($section_id > 0) {
        // section_id -> teacher_sections -> teacher_configs
        $stmt = $pdo->prepare("
            SELECT tc.difficulty
            FROM teacher_sections ts
            JOIN teacher_configs tc ON tc.teacher_id = ts.teacher_id
            WHERE ts.section_id = :sid
            LIMIT 1
        ");
        $stmt->execute([':sid' => $section_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

    } elseif ($section !== '') {
        // section name -> sections.id -> teacher_sections -> teacher_configs
        $stmt = $pdo->prepare("
            SELECT tc.difficulty
            FROM sections s
            JOIN teacher_sections ts ON ts.section_id = s.id
            JOIN teacher_configs tc ON tc.teacher_id = ts.teacher_id
            WHERE UPPER(TRIM(s.section_name)) = UPPER(TRIM(:sname))
            LIMIT 1
        ");
        $stmt->execute([':sname' => $section]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

    } elseif ($teacher_id > 0) {
        // direct teacher id -> teacher_configs
        $stmt = $pdo->prepare("SELECT difficulty FROM teacher_configs WHERE teacher_id = :tid LIMIT 1");
        $stmt->execute([':tid' => $teacher_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Provide section_id OR section OR teacher_id']);
        exit;
    }

    if (!$row) {
        // safe default: Medium (4-6)
        $defaultDifficulty = 1;
        $min = 4; $max = 6;
        echo json_encode([
            'success' => false,
            'message' => 'No difficulty config found; returning defaults (Medium).',
            'difficulty' => $defaultDifficulty,
            'question_min' => $min,
            'question_max' => $max
        ]);
        exit;
    }

    $diff = intval($row['difficulty']);
    if ($diff < 0 || $diff > 2) $diff = 1;

    switch ($diff) {
        case 0:
            $min = 1; $max = 3; $label = 'Easy';
            break;
        case 2:
            $min = 7; $max = 10; $label = 'Hard';
            break;
        default:
            $min = 4; $max = 6; $label = 'Medium';
            break;
    }

    echo json_encode([
        'success' => true,
        'difficulty' => $diff,
        'difficulty_label' => $label,
        'question_min' => $min,
        'question_max' => $max
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error', 'details' => $e->getMessage()]);
    exit;
}
