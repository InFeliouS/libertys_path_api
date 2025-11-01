<?php
declare(strict_types=1);

// register_student_sections.php
// Purpose: return all sections (id + name only) or validate one section.
// Unity-friendly: associative-only JSON, no numeric keys, minimal data.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

require_once __DIR__ . '/../../../src/config/db.php';

function normalize_section(string $s): string {
  $s = trim($s);
  $s = preg_replace('/\s+/u', ' ', $s);
  $s = mb_strtoupper($s);
  $s = preg_replace('/[\x{200B}-\x{200F}\p{C}]/u', '', $s);
  return $s;
}

try {
  $qSectionId = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
  $qSectionName = isset($_GET['section_name']) ? trim((string)$_GET['section_name']) : '';

  // ✅ Validate by ID
  if ($qSectionId > 0) {
    $stmt = $pdo->prepare('SELECT id AS section_id, section_name FROM sections WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $qSectionId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
      'success' => true,
      'exists' => $row ? true : false,
      'section' => $row ?: null
    ]);
    exit;
  }

  // ✅ Validate by Name (case-insensitive + trimmed)
  if ($qSectionName !== '') {
    $stmt = $pdo->prepare('SELECT id AS section_id, section_name FROM sections WHERE LOWER(TRIM(section_name)) = LOWER(TRIM(:nm)) LIMIT 1');
    $stmt->execute([':nm' => $qSectionName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      // fallback: normalization comparison
      $all = $pdo->query('SELECT id AS section_id, section_name FROM sections');
      $rows = $all->fetchAll(PDO::FETCH_ASSOC);
      $target = normalize_section($qSectionName);
      foreach ($rows as $r) {
        if (normalize_section((string)$r['section_name']) === $target) {
          $row = $r;
          break;
        }
      }
    }

    echo json_encode([
      'success' => true,
      'exists' => $row ? true : false,
      'section' => $row ?: null
    ]);
    exit;
  }

  // ✅ Default: Return all sections (for dropdown / dictionary)
  $stmt = $pdo->query('SELECT id AS section_id, section_name FROM sections ORDER BY section_name ASC');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'sections' => $rows
  ]);
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => 'Server error: ' . $e->getMessage()
  ]);
  exit;
}
