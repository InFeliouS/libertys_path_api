<?php
// src/dashboard/student_register.php

// only start session if none
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// must be teacher
if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Not authenticated'
    ]);
    exit;
}

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Must POST'
    ]);
    exit;
}

// always return JSON
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

// sanitize inputs
$given_name  = strtoupper(trim($_POST['given_name']  ?? ''));
$middle_name = strtoupper(trim($_POST['middle_name'] ?? ''));
$last_name   = strtoupper(trim($_POST['last_name']   ?? ''));
$section_id  = intval($_POST['section_id'] ?? 0);
$birth_sex   = $_POST['birth_sex'] ?? '';
if (!in_array($birth_sex, ['Male','Female'], true)) {
    $birth_sex = '';
}

// validation
if (
    !$given_name ||
    !$last_name ||
    !$section_id ||
    !$birth_sex
) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'All required fields must be filled.'
    ]);
    exit;
}

// compute the next student ID (so the JS preview matches what we’ll insert)
$nextId = null;
try {
    $r = $pdo->query("SHOW TABLE STATUS LIKE 'students'")->fetch(PDO::FETCH_ASSOC);
    $nextId = (int)$r['Auto_increment'];
} catch (\Throwable $e) {
    // fallback, let DB assign it
}

// generate credentials
$uBase = strtolower(substr($given_name,0,1)) . strtolower(str_replace(' ','',$last_name));
$username = $nextId
    ? $uBase . $nextId
    : $uBase;

$pBase = $middle_name ?: $given_name;
$pBase = str_replace(' ','',$pBase);
$passwordPlain = $nextId
    ? ucfirst(strtolower($pBase)) . $nextId
    : ucfirst(strtolower($pBase));

// hash it
$passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    // 1) students
    $stmt = $pdo->prepare("
        INSERT INTO students
          (given_name, middle_name, last_name, section_id, birth_sex)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $given_name,
        $middle_name ?: null,
        $last_name,
        $section_id,
        $birth_sex
    ]);
    $sid = $pdo->lastInsertId();

    // 2) accounts
    $stmt = $pdo->prepare("
        INSERT INTO student_accounts
          (student_id, username, password)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $sid,
        $username,
        $passwordHash
    ]);

    // 3) progress (leverage DB defaults for statuses & retries)
    $stmt = $pdo->prepare("
        INSERT INTO student_progress (student_id)
        VALUES (?)
    ");
    $stmt->execute([$sid]);

    $pdo->commit();

    // respond JSON
    echo json_encode([
        'success'  => true,
        'username' => $username,
        'password' => $passwordPlain
    ]);
    exit;
}
catch (\Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Registration failed: ' . $e->getMessage()
    ]);
    exit;
}
