<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/login.html");
    exit();
}

$teacher_username = $_SESSION['teacher_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
</head>
<body>
    <header>
        <h1>Teacher Dashboard</h1>
        <nav>
            <ul>
                <li><a href="student_monitoring.php">Student Monitoring</a></li>
                <li><a href="student_register.php">Register Student</a></li> <!-- ✅ New Link -->
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <h2>Welcome, Teacher!</h2>
        <p>Manage student progress and registrations here.</p>
    </main>
</body>
</html>

