<?php
/**
 * POST a team run to the leaderboard (run-outcome version).
 * Accepts JSON or x-www-form-urlencoded:
 * {
 *   "player1_name": "Alice",
 *   "player2_name": "Bob",
 *   "section_id":  11,          // preferred
 *   "section":     "NF TEST",   // fallback if no section_id
 *   "score":       2100,
 *   "time_left":   398,         // seconds
 *   "life_used":   0|1,         // 0 = PERFECT RUN, 1 = ONE LIFE USED
 *   "run_status":  "PERFECT RUN" | "ONE LIFE USED" (optional)
 * }
 */

declare(strict_types=1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
header('Content-Type: application/json; charset=utf-8');

try {
  require __DIR__ . '/../../../../src/config/db.php'; // $pdo (PDO)

  // Parse JSON (preferred) or fallback to POST
  $raw = file_get_contents('php://input');
  if ($raw !== false && trim($raw) !== '') {
    $d = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($d)) $_POST = $d;
  }

  $player1 = trim((string)($_POST['player1_name'] ?? ''));
  $player2 = trim((string)($_POST['player2_name'] ?? ''));
  if ($player1 === '' && $player2 === '') {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Missing player names.']); exit;
  }

  $score     = max(0, (int)($_POST['score'] ?? 0));
  $timeLeft  = max(0, (int)($_POST['time_left'] ?? 0));
  $lifeUsed  = (int)($_POST['life_used'] ?? 0); // default to 0 (perfect)
  $lifeUsed  = $lifeUsed > 0 ? 1 : 0;
  $runStatus = trim((string)($_POST['run_status'] ?? ''));
  if ($runStatus === '') $runStatus = $lifeUsed === 0 ? 'PERFECT RUN' : 'ONE LIFE USED';

  // Resolve section name (prefer section_id)
  $sectionId  = (int)($_POST['section_id'] ?? 0);
  $sectionStr = trim((string)($_POST['section'] ?? ''));
  if ($sectionId > 0) {
    $s = $pdo->prepare('SELECT section_name FROM sections WHERE id=:id');
    $s->execute([':id'=>$sectionId]);
    $section = (string)$s->fetchColumn();
    if ($section === '') { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Section not found.']); exit; }
  } else {
    if ($sectionStr === '') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Provide section_id or section.']); exit; }
    $section = $sectionStr;
  }

  $sql = "
    INSERT INTO leaderboard_team_runs
      (player1_name, player2_name, score, time_left, life_used, run_status, section, created_at)
    VALUES
      (:p1, :p2, :score, :tleft, :life_used, :run_status, :section, NOW())
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':p1'         => $player1,
    ':p2'         => $player2,
    ':score'      => $score,
    ':tleft'      => $timeLeft,
    ':life_used'  => $lifeUsed,
    ':run_status' => $runStatus,
    ':section'    => $section,
  ]);

  echo json_encode(['ok'=>true,'id'=>(int)$pdo->lastInsertId(),'section'=>$section], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
