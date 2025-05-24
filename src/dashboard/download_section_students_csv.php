<?php
// src/dashboard/download_section_students_csv.php

require_once __DIR__ . '/../config/db.php';

// 1) Validate section_id
$sectionId = isset($_GET['section_id']) ? (int) $_GET['section_id'] : 0;
if ($sectionId <= 0) {
    exit('Invalid section ID.');
}

// 2) Fetch student info + account_id
$stmt = $pdo->prepare("
    SELECT
      s.given_name,
      s.middle_name,
      s.last_name,
      sa.username,
      sa.account_id AS account_id
    FROM students s
    JOIN student_accounts sa
      ON sa.student_id = s.id
    WHERE s.section_id = ?
    ORDER BY s.last_name, s.given_name
");
$stmt->execute([$sectionId]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3) Send CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="section_' . $sectionId . '_students.csv"');

// 4) Open output stream & write header row
$output = fopen('php://output', 'w');
fputcsv($output, ['Given Name','Middle Name','Last Name','Username','Password']);

// 5) For each student, rebuild plain-text password
//    (same logic as in student_register.php: ucfirst(strtolower(base)) . account_id)
foreach ($students as $stu) {
    // base = middle_name if present, else given_name
    $pBase = $stu['middle_name'] ?: $stu['given_name'];
    $pBase = str_replace(' ', '', $pBase);
    $passwordPlain = ucfirst(strtolower($pBase)) . $stu['account_id'];

    fputcsv($output, [
        $stu['given_name'],
        $stu['middle_name'],
        $stu['last_name'],
        $stu['username'],
        $passwordPlain,
    ]);
}

fclose($output);
exit;
