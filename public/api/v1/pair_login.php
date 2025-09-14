<?php
// public/api/v1/pair_login.php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../../src/config/db.php';

function g($a, $k) { $v = $a[$k] ?? ''; return is_string($v) ? trim($v) : ''; }

// Accept JSON or form
$raw = file_get_contents('php://input') ?: '';
$in  = json_decode($raw, true);
if (!is_array($in) || empty($in)) $in = $_POST;

$u1 = g($in, 'username1');
$u2 = g($in, 'username2');

if ($u1 === '' || $u2 === '') {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Both usernames are required']); exit;
}
if (strcasecmp($u1, $u2) === 0) {
  http_response_code(422);
  echo json_encode(['success'=>false,'error'=>'Usernames must be different']); exit;
}

try {
  // Note: default utf8mb4_general_ci is case-insensitive, so '=' works
  $stmt = $pdo->prepare("
    SELECT sa.student_id, sa.username,
           st.section_id, st.given_name, st.middle_name, st.last_name,
           sec.section_name
    FROM student_accounts sa
    JOIN students st ON st.id = sa.student_id
    JOIN sections sec ON sec.id = st.section_id
    WHERE sa.username IN (?, ?)
  ");
  $stmt->execute([$u1, $u2]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($rows) !== 2) {
    // Figure out who is missing
    $found = array_map(fn($r)=>strtolower($r['username']), $rows);
    $miss  = [];
    if (!in_array(strtolower($u1), $found, true)) $miss[] = $u1;
    if (!in_array(strtolower($u2), $found, true)) $miss[] = $u2;
    http_response_code(404);
    echo json_encode(['success'=>false,'error'=>'Unknown username(s): '.implode(', ', $miss)]); exit;
  }

  // Enforce same-section rule
  $secIdA = (int)$rows[0]['section_id'];
  $secIdB = (int)$rows[1]['section_id'];
  if ($secIdA !== $secIdB) {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Players must belong to the same section']); exit;
  }

  // Stable order (sort by username) so client state is predictable
  usort($rows, fn($a,$b)=>strcasecmp($a['username'],$b['username']));

  $pairToken = bin2hex(random_bytes(16)); // opaque token for later score submit

  $resp = [
    'success'     => true,
    'pair_token'  => $pairToken,
    'section_id'  => $secIdA,
    'section'     => $rows[0]['section_name'],
    'player1'     => [
      'student_id' => (int)$rows[0]['student_id'],
      'username'   => $rows[0]['username'],
      'given_name' => $rows[0]['given_name'],
      'middle_name'=> $rows[0]['middle_name'],
      'last_name'  => $rows[0]['last_name'],
    ],
    'player2'     => [
      'student_id' => (int)$rows[1]['student_id'],
      'username'   => $rows[1]['username'],
      'given_name' => $rows[1]['given_name'],
      'middle_name'=> $rows[1]['middle_name'],
      'last_name'  => $rows[1]['last_name'],
    ]
  ];

  echo json_encode($resp, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
