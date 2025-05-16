<?php
// src/auth/login_process.php

// (Session is already started in public/index.php)

// Load the database connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        // Ensure the PDO instance is available
        if (!isset($pdo)) {
            throw new Exception("Database connection is not established.");
        }

        // Prepare and execute query
        $stmt = $pdo->prepare("
            SELECT id, username, password 
            FROM teachers 
            WHERE username = :username
        ");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify credentials
        if ($user && password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['teacher_id']       = $user['id'];
            $_SESSION['teacher_username'] = $user['username'];

            // Redirect to dashboard via front-controller
            header("Location: /dashboard");
            exit;
        } else {
            echo "Invalid username or password!";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Optionally close the connection
$pdo = null;
