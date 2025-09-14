<?php
/**
 * POST a team run to the leaderboard.
 *
 * Accepts JSON or x-www-form-urlencoded:
 * {
 *   "player1_name": "Alice",
 *   "player2_name": "Bob",
 *   "section_id":  10,              // preferred (numeric)
 *   "section":     "4A",            // fallback (string) if section_id omitted
 *   "score":       1234,
 *   "time_left":   87,              // seconds (int)
 *   "correct":     6,
 *   "mistakes":    0,               // 0 or 1
 *   "perfect":     1                // 0 or 1
 * }
 *
 * Response: { ok:true, id:<insert_id>, section:"<resolved section name>" }
 */

declare(strict_types=1);

// Basic CORS for Unity editor/testing (tweak/lock down as you wish)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../../../src/config/db.php'; // provides $pdo (PDO)

    // Parse JSON body (preferred) or fallback to POST
    $raw = file_get_contents('php://input');
    $isJson = false;
    if ($raw !== false && strlen(trim($raw)) > 0) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $_POST = $decoded;
            $isJson = true;
        }
    }

    // Input helpers
    $player1 = trim((string)($_POST['player1_name'] ?? ''));
    $player2 = trim((string)($_POST['player2_name'] ?? ''));
    $score   = (int)($_POST['score']      ?? 0);
    $tleft   = (int)($_POST['time_left']  ?? 0);
    $correct = (int)($_POST['correct']    ?? 0);
    $mist    = (int)($_POST['mistakes']   ?? 0);
    $perf    = (int)($_POST['perfect']    ?? 0);

    $sectionId  = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
    $sectionStr = trim((string)($_POST['section'] ?? ''));

    // Validate basic fields
    if ($player1 === '' && $player2 === '') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Missing player names.']);
        exit;
    }

    // Resolve section name
    $sectionName = '';
    if ($sectionId > 0) {
        $s = $pdo->prepare("SELECT section_name FROM sections WHERE id = :id");
        $s->execute([':id' => $sectionId]);
        $sectionName = (string)$s->fetchColumn();
        if ($sectionName === '') {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Section not found for section_id.']);
            exit;
        }
    } else {
        // Fallback to provided string (current schema uses text column "section")
        if ($sectionStr === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Provide section_id or section.']);
            exit;
        }
        $sectionName = $sectionStr;
    }

    // Clamp/normalize numbers
    $score   = max(0, $score);
    $tleft   = max(0, $tleft);
    $correct = max(0, $correct);
    $mist    = ($mist > 0) ? 1 : 0;
    $perf    = ($perf > 0) ? 1 : 0;

    // Insert
    $sql = "
        INSERT INTO leaderboard_team_runs
        (player1_name, player2_name, score, time_left, correct, mistakes, perfect, section, created_at)
        VALUES
        (:p1, :p2, :score, :tleft, :correct, :mistakes, :perfect, :section, NOW())
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':p1'       => $player1,
        ':p2'       => $player2,
        ':score'    => $score,
        ':tleft'    => $tleft,
        ':correct'  => $correct,
        ':mistakes' => $mist,
        ':perfect'  => $perf,
        ':section'  => $sectionName,
    ]);

    echo json_encode([
        'ok'           => true,
        'id'           => (int)$pdo->lastInsertId(),
        'section'      => $sectionName,
        'was_json'     => $isJson,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
