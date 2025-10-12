<?php
// src/auth/auth_guard.php — minimal auth + section-scope helpers
// NO declare(strict_types=1) to avoid placement issues

function start_session_once(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
}

function require_auth(): void {
  start_session_once();
  if (empty($_SESSION['teacher_id'])) {
    // Redirect back to login (dynamic base so subfolders work)
    $front = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $base  = rtrim(str_replace('\\','/', dirname($front)), '/'); // e.g. /libertys_path_api/public
    if (function_exists('ob_get_level') && ob_get_level() > 0) { ob_end_clean(); }
    header('Location: ' . $base . '/html/login.html');
    exit;
  }
}

function is_admin(): bool {
  return (($_SESSION['role'] ?? '') === 'ADMIN');
}

/**
 * Block non-admins from accessing sections not assigned to them.
 * - Admin: pass
 * - Teacher: $sectionId must be in $_SESSION['sections'] (array of ints)
 */
function enforce_section_scope(int $sectionId): void {
  if ($sectionId <= 0) return; // if not provided, let downstream code handle it
  if (is_admin()) return;
  $allowed = $_SESSION['sections'] ?? [];
  if (!in_array($sectionId, is_array($allowed) ? $allowed : [], true)) {
    http_response_code(403);
    exit('Forbidden (section scope)');
  }
}
