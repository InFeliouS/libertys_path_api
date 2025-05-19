<?php
// src/dashboard/update_section.php

// 0) Turn off notice‐level output so JSON stays clean
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE);

// 1) Start session only if none
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) JSON response header
header('Content-Type: application/json');

// 3) Authentication
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// 4) Parse & validate payload
$in = json_decode(file_get_contents('php://input'), true);
if (
    empty($in['section_id']) ||
    !isset($in['section_name'], $in['start_school_year'], $in['end_school_year'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$id    = (int)$in['section_id'];
$name  = trim($in['section_name']);
$start = trim($in['start_school_year']);
$end   = trim($in['end_school_year']);

if ($name === '' ||
    !preg_match('/^\d{4}$/', $start) ||
    !preg_match('/^\d{4}$/', $end)
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// 5) Update DB
try {
    $stmt = $pdo->prepare("
        UPDATE sections
           SET section_name      = ?,
               start_school_year = ?,
               end_school_year   = ?
         WHERE id = ?
    ");
    $stmt->execute([$name, $start, $end, $id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
