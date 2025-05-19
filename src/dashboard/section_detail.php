<?php
// src/dashboard/section_detail.php

// 1) Session & Auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['teacher_id'])) {
    header('Location: /login');
    exit;
}

// 2) Validate section_id
$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
if (!$section_id) {
    header('Location: /dashboard');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// 3) Fetch section meta (for header)
$stmt = $pdo->prepare("
  SELECT section_name, start_school_year, end_school_year
    FROM sections 
   WHERE id = ?
");
$stmt->execute([$section_id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$section) {
    header('Location: /dashboard');
    exit;
}

// 4) Fetch students + their progress
$sql = "
  SELECT
    st.id                 AS student_id,
    st.last_name,
    st.given_name,
    st.middle_name,
    st.birth_sex,
    sp.first_qpr_status,   sp.first_qpr_retries,
    sp.second_qpr_status,  sp.second_qpr_retries,
    sp.third_qpr_status,   sp.third_qpr_retries,
    sp.fourth_qpr_status,  sp.fourth_qpr_retries
  FROM students st
  LEFT JOIN student_progress sp
    ON sp.student_id = st.id
  WHERE st.section_id = ?
  ORDER BY st.last_name, st.given_name
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$section_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5) Map the raw ENUM strings → display text
$status_map = [
  'unvisited' => 'unvisited',
  'visited'   => 'visited',
  'complete'  => 'completed',
];

foreach ($students as &$s) {
  foreach (['first','second','third','fourth'] as $q) {
    $raw = $s["{$q}_qpr_status"];
    $s["{$q}_qpr_status_text"] = $status_map[$raw] ?? 'unknown';
  }
}
unset($s);

// 6) Render your HTML template (no path change)
include __DIR__ . '/../../public/html/section_detail.html';
