<?php
session_start();
require '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: /libertys_path_api/login");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $given_name = htmlspecialchars(trim($_POST['given_name']));
    $middle_name = isset($_POST['middle_name']) ? htmlspecialchars(trim($_POST['middle_name'])) : null;
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $section_name = htmlspecialchars(trim($_POST['section_name']));
    $birth_sex = htmlspecialchars(trim($_POST['birth_sex']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Validate required fields
    if (empty($given_name) || empty($last_name) || empty($section_name) || empty($birth_sex) || empty($username) || empty($password)) {
        die("All required fields must be filled out.");
    }

    // Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO students (given_name, middle_name, last_name, section_name, birth_sex, username, password) 
                              VALUES (:given_name, :middle_name, :last_name, :section_name, :birth_sex, :username, :password)");
        $stmt->bindParam(':given_name', $given_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':section_name', $section_name);
        $stmt->bindParam(':birth_sex', $birth_sex);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        // Execute and check
        if ($stmt->execute()) {
            echo "<script>alert('Student registered successfully!'); window.location.href='/libertys_path_api/dashboard';</script>";
        } else {
            echo "Error: Unable to register student.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    // If not a POST request, display the registration form
    include '../../html/register_student.html';
}

$pdo = null; // Close the connection
?>