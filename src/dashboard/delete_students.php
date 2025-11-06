<?php
// src/dashboard/delete_students.php
// Safe delete selected students (accepts JSON or form POST).
// - Accepts { ids: [1,2,3] } or { student_ids: [...] } or POST ids[].
// - Optional section scoping via ?section_id=XX or "section_id" in JSON/POST.
// - Uses transaction. Deletes student_progress (if exists), student_accounts, students.

error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();

// simple auth guard (keep as your app requires)
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

require_once __DIR__ . '/../config/db.php'; // provides $pdo (PDO)

// --- Read input (JSON body preferred) ---
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

// fallback to $_POST if not JSON
if (!is_array($input)) {
    $input = $_POST;
}

// Pull ids from either key
$ids = $input['student_ids'] ?? $input['ids'] ?? null;
// If form style with ids[] (array), $_POST gives array; keep it.
if (is_null($ids) && isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];
}

// Normalize IDs: ensure array of ints
if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload: no ids provided']);
    exit;
}

$ids = array_values(array_filter(array_map('intval', $ids), function($v){ return $v > 0; }));
if (count($ids) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload: no valid ids provided']);
    exit;
}

// Optional: section scoping to avoid deleting students from other sections.
// Accept from GET or input body
$sectionId = null;
if (isset($_GET['section_id'])) {
    $sectionId = (int) $_GET['section_id'];
} elseif (isset($input['section_id'])) {
    $sectionId = (int) $input['section_id'];
}
if ($sectionId <= 0) {
    $sectionId = null;
}

try {
    // If section scoping requested, filter ids to only those in that section.
    if ($sectionId !== null) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id FROM students WHERE id IN ($placeholders) AND section_id = ?");
        $execParams = array_merge($ids, [$sectionId]);
        $stmt->execute($execParams);
        $valid = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $valid = array_map('intval', $valid ?: []);
        if (count($valid) === 0) {
            // Nothing to delete in this section
            echo json_encode(['success' => true, 'deleted' => 0, 'note' => 'No matching students found in section']);
            exit;
        }
        $ids = $valid;
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Check if student_progress table exists — delete if present (non-fatal)
    $hasProgress = false;
    $res = $pdo->query("SHOW TABLES LIKE 'student_progress'");
    if ($res && $res->fetch()) {
        $hasProgress = true;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    if ($hasProgress) {
        $stmt = $pdo->prepare("DELETE FROM student_progress WHERE student_id IN ($placeholders)");
        $stmt->execute($ids);
    }

    // Delete from student_accounts (cascade also present in schema but explicit delete is fine)
    $stmt = $pdo->prepare("DELETE FROM student_accounts WHERE student_id IN ($placeholders)");
    $stmt->execute($ids);

    // Finally delete from students
    $stmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $deletedCount = $stmt->rowCount();

    $pdo->commit();

    echo json_encode(['success' => true, 'deleted' => $deletedCount]);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
