<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

if (empty($_FILES['csv']['tmp_name'])) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Missing csv file']); exit; }

$section_id = (int)($_POST['section_id'] ?? 0);
if ($section_id <= 0) { http_response_code(422); echo json_encode(['success'=>false,'error'=>'Missing section_id']); exit; }

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

$fh = fopen($_FILES['csv']['tmp_name'], 'r');
if (!$fh) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'Cannot open file']); exit; }

/*
CSV expected columns (with or without header):
Given Name, Middle Name, Last Name, Sex
Sex must be Male/Female (case-insensitive; m/f accepted).
*/
$inserted = 0; $first = true;
$stmt = $mysqli->prepare("INSERT INTO students (given_name, middle_name, last_name, section_id, birth_sex) VALUES (?,?,?,?,?)");

while (($row = fgetcsv($fh)) !== false) {
    // Header detect & skip
    if ($first) {
        $header = array_map('strtolower', $row);
        $looksHeader = in_array('given name', $header, true) || in_array('last name', $header, true);
        if ($looksHeader) { $first = false; continue; }
        $first = false;
    }

    $given  = trim((string)($row[0] ?? ''));
    $middle = trim((string)($row[1] ?? ''));
    $last   = trim((string)($row[2] ?? ''));
    $sex    = trim((string)($row[3] ?? ''));

    if ($given === '' || $last === '') continue;
    $l = strtolower($sex);
    if ($l === 'm' || $l === 'male') $sex = 'Male';
    elseif ($l === 'f' || $l === 'female') $sex = 'Female';
    else continue; // skip invalid sex

    $stmt->bind_param("sssiss", $given, $middle, $last, $section_id, $sex);
    // ^ OOPS: types must be "sssis": 3 strings, 1 int, 1 string
}
