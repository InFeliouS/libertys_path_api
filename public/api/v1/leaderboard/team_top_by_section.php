<?php
/**
 * GET top team runs for a given section.
 * Params:
 *  - section_id (int, required)
 *  - page (int, optional, 1-based)
 *  - limit (int, optional, default 10, max 100)
 *  - perfect_only (0|1) -> life_used = 0
 *  - date (YYYY-MM-DD) -> optional single-day filter (server local)
 *
 * Returns:
 *  - ok, section_id, section_name, data: [ ...rows... ], meta: { page, pageSize, total }
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

try {
  require __DIR__ . '/../../../../src/config/db.php'; // $pdo (PDO)

  $sid         = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;
  $page        = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $limitParam  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
  $perfectOnly = isset($_GET['perfect_only']) ? (int)$_GET['perfect_only'] : 0;
  $dateParam   = isset($_GET['date']) ? trim($_GET['date']) : null;

  if ($sid <= 0) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Missing section_id']); exit;
  }

  // normalize limit
  $limit = max(1, min(100, $limitParam));
  // default to 10 if no explicit limit provided
  if (!isset($_GET['limit'])) $limit = 10;

  $offset = ($page - 1) * $limit;

  // Resolve section name (authoritative from ID)
  $s = $pdo->prepare('SELECT section_name FROM sections WHERE id = :id');
  $s->execute([':id' => $sid]);
  $sectionName = (string)$s->fetchColumn();
  if ($sectionName === '') {
    http_response_code(404);
    echo json_encode(['ok'=>false,'error'=>'Section not found']); exit;
  }

  // Parse date filter if valid
  $useDate = false;
  if ($dateParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateParam)) {
    $useDate = true;
    $fromDate = $dateParam . ' 00:00:00';
    $toDate   = $dateParam . ' 23:59:59';
  }

  // Build WHERE clauses and params
  $whereClauses = ['section = :sectionName'];
  $params = [':sectionName' => $sectionName];

  if ($perfectOnly === 1) {
    $whereClauses[] = 'life_used = 0';
  }
  if ($useDate) {
    $whereClauses[] = 'created_at BETWEEN :fromDate AND :toDate';
    $params[':fromDate'] = $fromDate;
    $params[':toDate']   = $toDate;
  }

  $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);

  // Count total rows (for pagination meta)
  $countSql = "SELECT COUNT(1) FROM leaderboard_team_runs $whereSql";
  $countStmt = $pdo->prepare($countSql);
  foreach ($params as $k => $v) {
    $countStmt->bindValue($k, $v);
  }
  $countStmt->execute();
  $total = (int)$countStmt->fetchColumn();

  // Ordering: if filtering by date, show newest first; otherwise default ranking order
  $orderBy = $useDate ? 'created_at DESC' : 'score DESC, time_left DESC, created_at ASC';

  // Select page rows
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
    $whereSql
    ORDER BY $orderBy
    LIMIT :lim OFFSET :off
  ";
  $stmt = $pdo->prepare($sql);
  // bind named params
  foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
  }
  $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
  $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

  echo json_encode([
    'ok'           => true,
    'section_id'   => $sid,
    'section_name' => $sectionName,
    'data'         => $rows,
    'meta'         => [
      'page'     => $page,
      'pageSize' => $limit,
      'total'    => $total
    ]
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  // Do not leak stack traces in prod; this mirrors your existing behavior.
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
