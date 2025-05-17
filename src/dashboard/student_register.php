<?php
// src/dashboard/student_register.php

// (public/index.php) already started session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['teacher_id'])) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $given_name  = htmlspecialchars(trim($_POST['given_name']));
    $middle_name = trim($_POST['middle_name']) !== '' 
                 ? htmlspecialchars(trim($_POST['middle_name'])) 
                 : null;
    $last_name   = htmlspecialchars(trim($_POST['last_name']));
    $section_id  = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);
    $birth_sex   = htmlspecialchars(trim($_POST['birth_sex']));
    $username    = htmlspecialchars(trim($_POST['username']));
    $password    = $_POST['password'];

    // fail-fast if no section selected
    if (!$section_id) {
        header('Location: /register');
        exit;
    }

    // insert into students table
    $stmt = $pdo->prepare("
      INSERT INTO students
        (given_name, middle_name, last_name, section_id, birth_sex)
      VALUES
        (:g, :m, :l, :s, :x)
    ");
    $stmt->execute([
      ':g' => $given_name,
      ':m' => $middle_name,
      ':l' => $last_name,
      ':s' => $section_id,
      ':x' => $birth_sex
    ]);
    $studentId = $pdo->lastInsertId();

    // insert into student_accounts
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt2 = $pdo->prepare("
      INSERT INTO student_accounts
        (student_id, username, password)
      VALUES
        (:sid, :user, :pass)
    ");
    $stmt2->execute([
      ':sid'  => $studentId,
      ':user' => $username,
      ':pass' => $hash
    ]);

    header('Location: /dashboard');
    exit;
}

// GET → show the form
include __DIR__ . '/../../public/html/register_student.html';
