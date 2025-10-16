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
  // 1) Unique username
  $stmt = $pdo->prepare("SELECT 1 FROM teachers WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  if ($stmt->fetchColumn()) {
    http_response_code(400);
    echo "Username already exists."; exit;
  }

  // 2) Create teacher
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $ins  = $pdo->prepare("INSERT INTO teachers (username, password, first_name, last_name, role) VALUES (?, ?, ?, ?, 'TEACHER')");
  $ins->execute([$username, $hash, $first_name, $last_name]);
  $newTeacherId = (int)$pdo->lastInsertId();

  // 3) Assign sections (only if NOT already taken by another teacher)
  if (!empty($sectionIds)) {
    $sectionIds = array_values(array_unique(array_map('intval', $sectionIds)));
    $sectionIds = array_filter($sectionIds, static fn($v) => $v > 0);

    if ($sectionIds) {
      $placeholders = implode(',', array_fill(0, count($sectionIds), '?'));
      $sqlTaken = "SELECT section_id FROM teacher_sections WHERE section_id IN ($placeholders)";
      $stTaken = $pdo->prepare($sqlTaken);
      $stTaken->execute($sectionIds);
      $already = $stTaken->fetchAll(PDO::FETCH_COLUMN, 0);

      if ($already && count($already) > 0) {
        // Remove taken ones so we don't duplicate
        $sectionIds = array_values(array_diff($sectionIds, array_map('intval', $already)));
      }

      if ($sectionIds) {
        $ins2 = $pdo->prepare("INSERT INTO teacher_sections (teacher_id, section_id) VALUES (?, ?)");
        foreach ($sectionIds as $sid) {
          $ins2->execute([$newTeacherId, $sid]);
        }
      }
    }
  }

  // 4) Done
  header("Location: ./index.php?r=dashboard");
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo "Error: " . htmlspecialchars($e->getMessage());
}
