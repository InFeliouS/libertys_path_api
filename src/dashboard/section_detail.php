<?php
// src/dashboard/section_detail.php

// 1) Session & authorization
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ./index.php?r=login");
    exit;
}

// 2) Validate section_id
$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
if (!$section_id) {
    header("Location: ./index.php?r=dashboard");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// 3) Fetch section info
$stmt = $pdo->prepare("
    SELECT section_name, start_school_year, end_school_year
      FROM sections
     WHERE id = ?
");
$stmt->execute([$section_id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$section) {
    header("Location: ./index.php?r=dashboard");
    exit;
}

// 4) Fetch students + progress + wrong-attempt counts
$sql = "
SELECT
  st.id                   AS student_id,
  st.last_name,
  st.given_name,
  st.middle_name,
  st.birth_sex,

  -- QPR status & retries
  sp.first_qpr_status,   sp.first_qpr_retries,
  sp.second_qpr_status,  sp.second_qpr_retries,
  sp.third_qpr_status,   sp.third_qpr_retries,
  sp.fourth_qpr_status,  sp.fourth_qpr_retries,

  -- 1st-room wrong attempts
  fqa.q1_attempts AS first_q1_attempts,
  fqa.q2_attempts AS first_q2_attempts,
  fqa.q3_attempts AS first_q3_attempts,
  fqa.q4_attempts AS first_q4_attempts,
  fqa.q5_attempts AS first_q5_attempts,
  fqa.q6_attempts AS first_q6_attempts,

  -- 2nd-room wrong attempts
  sqa.bench1_attempts AS second_q1_attempts,
  sqa.bench2_attempts AS second_q2_attempts,
  sqa.bench3_attempts AS second_q3_attempts,
  sqa.bench4_attempts AS second_q4_attempts,
  sqa.bench5_attempts AS second_q5_attempts,
  sqa.bench6_attempts AS second_q6_attempts,

  -- 3rd-room wrong attempts
  tqa.question1 AS third_q1_attempts,
  tqa.question2 AS third_q2_attempts,
  tqa.question3 AS third_q3_attempts,
  tqa.question4 AS third_q4_attempts,
  tqa.question5 AS third_q5_attempts,
  tqa.question6 AS third_q6_attempts,

  -- 4th-room wrong attempts
  foqa.question1 AS fourth_q1_attempts,
  foqa.question2 AS fourth_q2_attempts,
  foqa.question3 AS fourth_q3_attempts,
  foqa.question4 AS fourth_q4_attempts,
  foqa.question5 AS fourth_q5_attempts,
  foqa.question6 AS fourth_q6_attempts

FROM students st
LEFT JOIN student_progress sp  ON sp.student_id = st.id
LEFT JOIN first_qpr_attempts  fqa ON fqa.student_id  = st.id
LEFT JOIN second_qpr_attempts sqa ON sqa.student_id  = st.id
LEFT JOIN third_qpr_attempts  tqa ON tqa.student_id  = st.id
LEFT JOIN fourth_qpr_attempts foqa ON foqa.student_id = st.id

WHERE st.section_id = ?
ORDER BY st.last_name, st.given_name
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$section_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5) Map raw ENUMs to display text
$status_map = [
  'unvisited' => 'Unvisited',
  'visited'   => 'Visited',
  'complete'  => 'Completed',
];
foreach ($students as &$s) {
  foreach (['first','second','third','fourth'] as $q) {
    $raw = $s["{$q}_qpr_status"];
    $s["{$q}_qpr_status_text"] = $status_map[$raw] ?? ucfirst($raw);
  }
}
unset($s);

// 6) Render HTML
include __DIR__ . '/../../public/html/section_detail.html';
