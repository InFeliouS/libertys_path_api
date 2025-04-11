<?php
session_start();
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $given_name = $_POST['given_name'];
    $middle_name = $_POST['middle_name'] ?? null;
    $last_name = $_POST['last_name'];
    $section_name = $_POST['section_name'];
    $birth_sex = $_POST['birth_sex'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    try {
        $stmt = $pdo->prepare("INSERT INTO students (given_name, middle_name, last_name, section_name, birth_sex, username, password) 
                                VALUES (:given_name, :middle_name, :last_name, :section_name, :birth_sex, :username, :password)");

        $stmt->bindParam(':given_name', $given_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':section_name', $section_name);
        $stmt->bindParam(':birth_sex', $birth_sex);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo "<script>alert('Student registered successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error registering student!');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <h2>Register New Student</h2>
    <form action="student_register.php" method="POST">
        <label>Given Name*: <input type="text" name="given_name" required></label><br>
        <label>Middle Name: <input type="text" name="middle_name"></label><br>
        <label>Last Name*: <input type="text" name="last_name" required></label><br>
        <label>Section Name*: <input type="text" name="section_name" required></label><br>
        <label>Birth Sex*: 
            <input type="radio" name="birth_sex" value="Male" required> Male
            <input type="radio" name="birth_sex" value="Female" required> Female
        </label><br>
        <label>Username*: <input type="text" name="username" required></label><br>
        <label>Password*: <input type="password" name="password" required></label><br>
        <button type="submit">Register Student</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
