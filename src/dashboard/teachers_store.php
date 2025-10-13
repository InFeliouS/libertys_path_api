<?php
// src/dashboard/teachers_store.php
require_once __DIR__ . '/../config/db.php'; // $pdo

header('Content-Type: text/html; charset=utf-8');

$username   = trim($_POST['username'] ?? '');
$password   = (string)($_POST['password'] ?? '');
$confirm    = (string)($_POST['confirm'] ?? '');
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$sectionIds = isset($_POST['section_ids']) && is_array($_POST['section_ids']) ? $_POST['section_ids'] : [];

if ($username === '' || $password === '' || $confirm === '') {
  http_response_code(400);
  echo "Missing required fields."; exit;
}
if ($password !== $confirm) {
  http_response_code(400);
  echo "Passwords do not match."; exit;
}

try {
  // unique username
  $stmt = $pdo->prepare("SELECT 1 FROM teachers WHERE username = ?");
  $stmt->execute([$username]);
  if ($stmt->fetchColumn()) {
    http_response_code(409);
    echo "Username already exists."; exit;
  }

  $hash = password_hash($password, PASSWORD_BCRYPT);

  // Insert teacher with first/last name (nullable)
  $stmt = $pdo->prepare("
    INSERT INTO teachers (username, first_name, last_name, password, role)
    VALUES (:u, :fn, :ln, :p, 'TEACHER')
  ");
  $stmt->execute([
    ':u'  => $username,
    ':fn' => ($first_name !== '' ? $first_name : null),
    ':ln' => ($last_name  !== '' ? $last_name  : null),
    ':p'  => $hash,
  ]);
  $newTeacherId = (int)$pdo->lastInsertId();

  // Optional: assign sections
  if (!empty($sectionIds)) {
    $ins = $pdo->prepare("INSERT INTO teacher_sections (teacher_id, section_id) VALUES (?, ?)");
    foreach ($sectionIds as $sid) {
      $sid = (int)$sid;
      if ($sid > 0) { $ins->execute([$newTeacherId, $sid]); }
    }
  }

  header("Location: ./index.php?r=dashboard");
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo "Error: " . htmlspecialchars($e->getMessage());
}
