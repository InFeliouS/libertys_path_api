<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['teacher_id'])) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$ct   = $_SERVER['CONTENT_TYPE'] ?? '';
$data = (stripos($ct, 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST;
$data = is_array($data) ? $data : [];

/* Accept new or legacy field names */
$id           = (int)($data['id'] ?? 0);
$given_name   = trim((string)($data['given_name'] ?? ''));
$middle_name  = trim((string)($data['middle_name'] ?? ''));
$last_name    = trim((string)($data['last_name'] ?? ''));
$birth_sex    = trim((string)($data['birth_sex'] ?? ''));

if (!$given_name && isset($data['student_name'])) {
    $parts = preg_split('/\s+/', trim((string)$data['student_name']));
    if ($parts) {
        $given_name  = $parts[0] ?? '';
        $last_name   = $parts[count($parts)-1] ?? '';
        if (count($parts) > 2) $middle_name = implode(' ', array_slice($parts, 1, -1));
    }
}
if (!$birth_sex && isset($data['sex'])) $birth_sex = $data['sex'];

$validSex = ['Male','Female'];
if ($birth_sex !== '' && !in_array($birth_sex, $validSex, true)) {
    $l = strtolower($birth_sex);
    if ($l === 'm' || $l === 'male')   $birth_sex = 'Male';
    elseif ($l === 'f' || $l === 'female') $birth_sex = 'Female';
    else $birth_sex = '';
}

if ($id <= 0 || $given_name === '' || $last_name === '' || $birth_sex === '') {
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing fields (id, given_name, last_name, birth_sex)']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
if (!isset($mysqli)) { http_response_code(500); echo json_encode(['success'=>false,'error'=>'DB not initialized']); exit; }

$stmt = $mysqli->prepare("UPDATE students
                          SET given_name=?, middle_name=?, last_name=?, birth_sex=?
                          WHERE id=?");
$stmt->bind_param("ssssi", $given_name, $middle_name, $last_name, $birth_sex, $id);
$ok = $stmt->execute();
$stmt->close();

if (!$ok) { http_response_code(500); echo json_encode(['success'=>false,'error'=>$mysqli->error]); exit; }
echo json_encode(['success'=>true]);
