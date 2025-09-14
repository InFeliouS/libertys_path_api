<?php
/**
 * GET top leaderboard rows for a given section_id.
 * Your DB today:
 *   - sections(id, section_name, ...)
 *   - leaderboard_team_runs(section, score, time_left, correct, mistakes, perfect, created_at, player1_name, player2_name)
 *
 * Query:
 *   GET ?section_id=10&limit=50&perfect_only=0
 *
 * Response:
 *   { ok: true, section_id, section_name, data: [ {...} ] }
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

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    if ($limit < 1)  $limit = 1;
    if ($limit > 200) $limit = 200;

    $perfectOnly = isset($_GET['perfect_only']) && (string)$_GET['perfect_only'] === '1';

    require_once __DIR__ . '/../../../../src/config/db.php'; // $pdo

    // Resolve section_name from sections table
    $secStmt = $pdo->prepare("SELECT section_name FROM sections WHERE id = :sid");
    $secStmt->execute([':sid' => $sid]);
    $sectionName = $secStmt->fetchColumn();

    if (!$sectionName) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Section not found']);
        exit;
    }

    // Pull leaderboard rows for that section (text match to current schema)
    $sql = "
        SELECT
            id,
            player1_name,
            player2_name,
            score,
            time_left,
            correct,
            mistakes,
            perfect,
            section,
            created_at
        FROM leaderboard_team_runs
        WHERE section = :sname
        " . ($perfectOnly ? "AND perfect = 1" : "") . "
        ORDER BY score DESC, time_left DESC, created_at ASC
        LIMIT :lim
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':sname', $sectionName, PDO::PARAM_STR);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    echo json_encode([
        'ok'           => true,
        'section_id'   => $sid,
        'section_name' => $sectionName,
        'data'         => $rows,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
