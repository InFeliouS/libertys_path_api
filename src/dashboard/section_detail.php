<?php
// src/dashboard/section_detail.php

// (Session already started)
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /login");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Validate section_id query parameter
$secId = filter_input(INPUT_GET, 'section_id', FILTER_VALIDATE_INT);
if (!$secId) {
    header("Location: /dashboard");
    exit;
}

// Fetch section info
$stmt = $pdo->prepare("
  SELECT section_name, start_school_year, end_school_year
  FROM sections
  WHERE id = ?
");
$stmt->execute([$secId]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$section) {
    header("Location: /dashboard");
    exit;
}

// Fetch students in that section
$stmt2 = $pdo->prepare("
  SELECT s.last_name,
         sec.section_name,
         s.birth_sex,
         sa.username
  FROM students AS s
  JOIN sections AS sec ON s.section_id = sec.id
  JOIN student_accounts AS sa ON sa.student_id = s.id
  WHERE s.section_id = ?
  ORDER BY s.last_name
");
$stmt2->execute([$secId]);
$students = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Render the Section Detail view
include __DIR__ . '/../../public/html/section_detail.html';
