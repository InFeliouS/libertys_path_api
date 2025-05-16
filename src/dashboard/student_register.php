<?php
// src/dashboard/student_register.php

// Session is already started in public/index.php, so only start if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect the route: only logged-in teachers can register students
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /login");
    exit;
}

// Load the database connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $given_name   = htmlspecialchars(trim($_POST['given_name']));
    $middle_name  = !empty($_POST['middle_name'])
                  ? htmlspecialchars(trim($_POST['middle_name']))
                  : null;
    $last_name    = htmlspecialchars(trim($_POST['last_name']));
    $section_name = htmlspecialchars(trim($_POST['section_name']));
    $birth_sex    = htmlspecialchars(trim($_POST['birth_sex']));
    $username     = htmlspecialchars(trim($_POST['username']));
    $password     = $_POST['password'];

    // Hash the password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO students
              (given_name, middle_name, last_name, section_name, birth_sex, username, password)
            VALUES
              (:given_name, :middle_name, :last_name, :section_name, :birth_sex, :username, :password)
        ");
        $stmt->bindParam(':given_name',   $given_name);
        $stmt->bindParam(':middle_name',  $middle_name);
        $stmt->bindParam(':last_name',    $last_name);
        $stmt->bindParam(':section_name', $section_name);
        $stmt->bindParam(':birth_sex',    $birth_sex);
        $stmt->bindParam(':username',     $username);
        $stmt->bindParam(':password',     $hashed);

        if ($stmt->execute()) {
            // On success, go back to the dashboard
            header("Location: /dashboard");
            exit;
        } else {
            echo "Error registering student.";
        }
    } catch (PDOException $e) {
        echo "DB Error: " . $e->getMessage();
    }
} else {
    // If someone visits /register/process via GET, just show the form
    include __DIR__ . '/../../public/html/register_student.html';
}
