<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../../src/config/db.php';

function norm($v){ return is_string($v) ? trim($v) : ''; }

// Accept JSON or form
$raw = file_get_contents('php://input') ?: '';
$in  = json_decode($raw, true);
if (!is_array($in) || empty($in)) $in = $_POST;

$given  = norm($in['given_name']  ?? '');
$middle = norm($in['middle_name'] ?? '');
$last   = norm($in['last_name']   ?? '');
$sectId = isset($in['section_id']) ? (int)$in['section_id'] : 0;
$sectNm = norm($in['section_name'] ?? '');

if ($given === '' || $last === '' || ($sectId <= 0 && $sectNm === '')) {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Missing required fields']); exit;
}

try {
  // Resolve section by id or name
  if ($sectId > 0) {
    $s = $pdo->prepare("SELECT id, section_name FROM sections WHERE id=?");
    $s->execute([$sectId]);
  } else {
    $s = $pdo->prepare("SELECT id, section_name FROM sections WHERE LOWER(section_name)=LOWER(?)");
    $s->execute([$sectNm]);
  }
  $sec = $s->fetch(PDO::FETCH_ASSOC);
  if (!$sec) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Section does not exist']); exit; }
  $section_id = (int)$sec['id'];

  // Canonicalize names (store uppercase)
  $givenU  = mb_strtoupper($given);
  $middleU = mb_strtoupper($middle);
  $lastU   = mb_strtoupper($last);

  // Username: first of given + first of middle (optional) + last (no spaces/dash/apos), lower
  $first = mb_substr($givenU, 0, 1);
  $mid   = ($middleU !== '') ? mb_substr($middleU, 0, 1) : '';
  $base  = strtolower($first . $mid . preg_replace('/[\s\-\'"]+/u', '', $lastU));

  // Ensure unique (suffix 2..n)
  $uname = $base; $n=2;
  $chk = $pdo->prepare("SELECT 1 FROM student_accounts WHERE username=?");
  while (true) { $chk->execute([$uname]); if ($chk->fetchColumn()===false) break; $uname = $base.$n++; }

  $pdo->beginTransaction();

  // Insert student (no sex column now)
  $insS = $pdo->prepare("
    INSERT INTO students (given_name, middle_name, last_name, section_id, created_at)
    VALUES (?, ?, ?, ?, NOW())
  ");
  $insS->execute([$givenU, $middleU, $lastU, $section_id]);
  $student_id = (int)$pdo->lastInsertId();

  // Insert account (no password column now)
  $insA = $pdo->prepare("
    INSERT INTO student_accounts (student_id, username, created_at)
    VALUES (?, ?, NOW())
  ");
  $insA->execute([$student_id, $uname]);

  $pdo->commit();

  echo json_encode([
    'success'=>true,
    'student_id'=>$student_id,
    'username'=>$uname,
    'section_id'=>$section_id,
    'section'=>$sec['section_name'] ?? ''
  ]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
