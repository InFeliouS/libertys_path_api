<?php
// src/dashboard/delete_section.php

// 0) Silence notices
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE);

// 1) Start session if none
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) JSON header
header('Content-Type: application/json');

// 3) Authentication
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// 4) Parse payload
$in = json_decode(file_get_contents('php://input'), true);
if (empty($in['section_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing section_id']);
    exit;
}

$id = (int)$in['section_id'];
require_once __DIR__ . '/../config/db.php';

try {
    $pdo->beginTransaction();

    // a) Grab student IDs
    $stmt = $pdo->prepare("SELECT id FROM students WHERE section_id = ?");
    $stmt->execute([$id]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($ids) {
        $ph = implode(',', array_fill(0, count($ids), '?'));

        // b) Delete progress
        $pdo->prepare("DELETE FROM student_progress WHERE student_id IN ($ph)")
            ->execute($ids);

        // c) Delete accounts
        $pdo->prepare("DELETE FROM student_accounts WHERE student_id IN ($ph)")
            ->execute($ids);

        // d) Delete students
        $pdo->prepare("DELETE FROM students WHERE id IN ($ph)")
            ->execute($ids);
    }

    // e) Delete the section
    $pdo->prepare("DELETE FROM sections WHERE id = ?")
        ->execute([$id]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
