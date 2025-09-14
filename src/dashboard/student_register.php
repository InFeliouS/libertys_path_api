<?php
// src/dashboard/student_register.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['teacher_id'])) {
  http_response_code(401);
  echo json_encode(['success'=>false,'error'=>'Not authenticated']);
  exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/student_helpers.php';

function g(array $a, string $k): string {
  $v = $a[$k] ?? '';
  return is_string($v) ? trim($v) : '';
}

$given  = g($_POST, 'given_name');
$middle = g($_POST, 'middle_name');
$last   = g($_POST, 'last_name');
$sex    = g($_POST, 'birth_sex');
$sectId = (int)($_POST['section_id'] ?? 0);

if ($given === '' || $last === '' || $sectId <= 0) {
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>'Missing required fields']);
  exit;
}

try {
  // section must exist
  $s = $pdo->prepare("SELECT id, section_name FROM sections WHERE id = ?");
  $s->execute([$sectId]);
  if (!$s->fetch(PDO::FETCH_ASSOC)) {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Section does not exist']);
    exit;
  }

  // canonicalize names
  $givenU  = mb_strtoupper($given);
  $middleU = mb_strtoupper($middle);
  $lastU   = mb_strtoupper($last);

  // username: jmcruz / jcruz + numeric suffix if taken (NO student number)
  $first = mb_substr($givenU, 0, 1);
  $mid   = ($middleU !== '') ? mb_substr($middleU, 0, 1) : '';
  $base  = strtolower($first . $mid . preg_replace('/\s+|-|\'/u', '', $lastU));
  $uname = $base;
  $try = 2;
  $chk = $pdo->prepare("SELECT 1 FROM student_accounts WHERE username = ?");
  while (true) {
    $chk->execute([$uname]);
    if ($chk->fetchColumn() === false) break;
    $uname = $base . $try;
    $try++;
  }

  // password: keep your existing flavor, but without student number -> add random 4 digits
  $seed = $middle !== '' ? $middle : $given;
  $seed = preg_replace('/\s+/', '', strtolower($seed));
  $passwordPlain = ucfirst($seed) . random_int(1000, 9999);
  $passwordHash  = password_hash($passwordPlain, PASSWORD_DEFAULT);

  $pdo->beginTransaction();

  $insS = $pdo->prepare("
    INSERT INTO students (given_name, middle_name, last_name, section_id, birth_sex, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
  ");
  $insS->execute([$givenU, $middleU, $lastU, $sectId, $sex !== '' ? $sex : null]);
  $student_id = (int)$pdo->lastInsertId();

  $insA = $pdo->prepare("
    INSERT INTO student_accounts (student_id, username, password_hash, created_at)
    VALUES (?, ?, ?, NOW())
  ");
  $insA->execute([$student_id, $uname, $passwordHash]);

  insert_attempt_rows_if_missing($pdo, $student_id);

  $pdo->commit();
  echo json_encode(['success'=>true,'student_id'=>$student_id,'username'=>$uname,'password'=>$passwordPlain]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
