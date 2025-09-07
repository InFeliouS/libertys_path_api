<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['teacher_id'])) { header('Location: index.php?route=login'); exit; }

$section_id = (int)($_GET['section_id'] ?? 0);
if ($section_id <= 0) { http_response_code(400); echo "Missing section_id"; exit; }

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo "DB not initialized"; exit; }

$stmt = $mysqli->prepare("SELECT s.given_name, s.middle_name, s.last_name, s.birth_sex, sec.section_name
                          FROM students s
                          JOIN sections sec ON sec.id = s.section_id
                          WHERE s.section_id=?
                          ORDER BY s.last_name, s.given_name, s.middle_name");
$stmt->bind_param("i", $section_id);
$stmt->execute();
$res = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="section_'.$section_id.'_students.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Section', 'Given Name', 'Middle Name', 'Last Name', 'Sex']);
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
        $row['section_name'],
        $row['given_name'],
        $row['middle_name'],
        $row['last_name'],
        $row['birth_sex'],
    ]);
}
fclose($out);
$stmt->close();
