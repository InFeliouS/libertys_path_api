<?php
/**
 * GET top team runs for a given section.
 * Params:
 *  - section_id (int, required)
 *  - limit (int, default 50, max 100)
 *  - perfect_only (0|1) -> life_used = 0
 *
 * Returns fields the Section leaderboard needs, now using life_used/run_status.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

try {
  require __DIR__ . '/../../../../src/config/db.php'; // $pdo (PDO)

  $sid         = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
  $limit       = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;
  $perfectOnly = isset($_GET['perfect_only']) ? (int)$_GET['perfect_only'] : 0;

  if ($sid <= 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Missing section_id']); exit;
  }

  // Resolve section name (authoritative from ID)
  $s = $pdo->prepare('SELECT section_name FROM sections WHERE id = :id');
  $s->execute([':id' => $sid]);
  $sectionName = (string)$s->fetchColumn();
  if ($sectionName === '') {
    http_response_code(404);
    echo json_encode(['ok'=>false,'error'=>'Section not found']); exit;
  }

  // Build WHERE
  $where = 'WHERE section = :sectionName';
  if ($perfectOnly === 1) {
    $where .= ' AND life_used = 0';   // new semantics
  }

  // Select only the fields the table renders
  $sql = "
    SELECT
      player1_name,
      player2_name,
      score,
      time_left,
      life_used,
      COALESCE(run_status, CASE WHEN life_used = 0 THEN 'PERFECT RUN' ELSE 'ONE LIFE USED' END) AS run_status,
      created_at
    FROM leaderboard_team_runs
    $where
    ORDER BY score DESC, time_left DESC, created_at ASC
    LIMIT :lim
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':sectionName', $sectionName, PDO::PARAM_STR);
  $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

  echo json_encode([
    'ok'           => true,
    'section_id'   => $sid,
    'section_name' => $sectionName,
    'data'         => $rows
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
