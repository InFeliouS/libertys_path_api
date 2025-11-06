<?php
// src/dashboard/update_section.php
// Update section metadata AND (optionally) assigned teacher in teacher_sections.

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Require auth (teacher must be logged in)
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Read JSON input
$in = json_decode(file_get_contents('php://input'), true);
if (
    empty($in['section_id']) ||
    !isset($in['section_name'], $in['start_school_year'], $in['end_school_year'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$sectionId = (int)$in['section_id'];
$name      = trim($in['section_name']);
$start     = trim($in['start_school_year']);
$end       = trim($in['end_school_year']);

// teacher_id optional: null/empty string => unassign
$teacherIdRaw = $in['teacher_id'] ?? null;
$teacherId = ($teacherIdRaw === null || $teacherIdRaw === '') ? null : (int)$teacherIdRaw;

if ($name === '' ||
    !preg_match('/^\d{4}$/', $start) ||
    !preg_match('/^\d{4}$/', $end)
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    // 0) Validate that the section exists (avoid updating non-existent rows)
    $chkSec = $pdo->prepare("SELECT id FROM sections WHERE id = :id LIMIT 1");
    $chkSec->execute([':id' => $sectionId]);
    $secRow = $chkSec->fetch(PDO::FETCH_ASSOC);
    if (!$secRow) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Section not found']);
        exit;
    }

    // 0.5) If teacher_id provided, validate teacher exists and is a TEACHER
    if ($teacherId !== null) {
        $chkT = $pdo->prepare("SELECT id FROM teachers WHERE id = :id AND role = 'TEACHER' LIMIT 1");
        $chkT->execute([':id' => $teacherId]);
        $tRow = $chkT->fetch(PDO::FETCH_ASSOC);
        if (!$tRow) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid teacher_id']);
            exit;
        }
    }

    $pdo->beginTransaction();

    // 1) Update section metadata
    $stmt = $pdo->prepare("
        UPDATE sections
           SET section_name      = :name,
               start_school_year = :start,
               end_school_year   = :end
         WHERE id = :id
    ");
    $stmt->execute([
        ':name'  => $name,
        ':start' => $start,
        ':end'   => $end,
        ':id'    => $sectionId,
    ]);

    // 2) Upsert teacher_sections mapping (or delete if unassign)
    // Check if a teacher_sections row exists for this section
    $check = $pdo->prepare("SELECT id, teacher_id FROM teacher_sections WHERE section_id = :sid LIMIT 1");
    $check->execute([':sid' => $sectionId]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    $assignmentResult = ['action' => 'none'];

    if ($teacherId === null) {
        // Unassign: remove mapping if exists
        if ($existing) {
            $del = $pdo->prepare("DELETE FROM teacher_sections WHERE id = :id");
            $del->execute([':id' => (int)$existing['id']]);
            $assignmentResult = [
                'action' => 'deleted',
                'teacher_section_id' => (int)$existing['id'],
                'previous_teacher_id' => isset($existing['teacher_id']) ? (int)$existing['teacher_id'] : null
            ];
        } else {
            $assignmentResult = ['action' => 'none']; // nothing to remove
        }
    } else {
        // Assign or reassign: prefer UPDATE, fallback to INSERT
        if ($existing) {
            // Update existing mapping if teacher differs
            if ((int)$existing['teacher_id'] === $teacherId) {
                $assignmentResult = [
                    'action' => 'nochange',
                    'teacher_section_id' => (int)$existing['id'],
                    'teacher_id' => $teacherId
                ];
            } else {
                $upd = $pdo->prepare("UPDATE teacher_sections SET teacher_id = :tid WHERE id = :id");
                $upd->execute([':tid' => $teacherId, ':id' => (int)$existing['id']]);
                $assignmentResult = [
                    'action' => 'updated',
                    'teacher_section_id' => (int)$existing['id'],
                    'teacher_id' => $teacherId,
                    'previous_teacher_id' => isset($existing['teacher_id']) ? (int)$existing['teacher_id'] : null
                ];
            }
        } else {
            // Insert new mapping
            $ins = $pdo->prepare("INSERT INTO teacher_sections (teacher_id, section_id) VALUES (:tid, :sid)");
            $ins->execute([':tid' => $teacherId, ':sid' => $sectionId]);
            $newId = (int)$pdo->lastInsertId();
            $assignmentResult = [
                'action' => 'inserted',
                'teacher_section_id' => $newId,
                'teacher_id' => $teacherId
            ];
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'section_id' => $sectionId,
        'assignment' => $assignmentResult
    ], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
