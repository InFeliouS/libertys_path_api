<?php
// public/api/v1/sections.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../../../src/config/db.php';

// Use session only if already started upstream; otherwise start it safely.
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

try {
  // Default: return all sections (original behavior)
  $sql  = "
    SELECT id, section_name, start_school_year, end_school_year
    FROM sections
    ORDER BY section_name ASC
  ";
  $params = [];
  
  // Minimal filter: if logged in as TEACHER and has assigned sections, restrict
  $role   = $_SESSION['role']    ?? null;
  $idsRaw = $_SESSION['sections'] ?? null;

  if ($role === 'TEACHER' && is_array($idsRaw) && count($idsRaw) > 0) {
    $ids = array_map('intval', $idsRaw);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "
      SELECT id, section_name, start_school_year, end_school_year
      FROM sections
      WHERE id IN ($placeholders)
      ORDER BY section_name ASC
    ";
    $params = $ids;
  }

  if ($params) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  } else {
    $stmt = $pdo->query($sql);
  }

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  echo json_encode(['success' => true, 'sections' => $rows], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
