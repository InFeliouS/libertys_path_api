<?php
session_start();
require '../config/db.php'; // Ensure path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Verify that $pdo is set
        if (!isset($pdo)) {
            throw new Exception("Database connection is not established.");
        }

        // Prepare SQL query
        $stmt = $pdo->prepare("SELECT id, username, password FROM teachers WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists, verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['teacher_id'] = $user['id'];
            $_SESSION['teacher_username'] = $user['username'];
            header("Location: /libertys_path_api/dashboard");
            exit();
        } else {
            echo "Invalid username or password!";
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "General Error: " . $e->getMessage();
    }
}

$pdo = null; // Close connection
?>
