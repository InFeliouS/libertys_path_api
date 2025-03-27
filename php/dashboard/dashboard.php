<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/login.html"); 
    exit();
}

$teacher_username = $_SESSION['teacher_username'];

// Load the dashboard HTML
include '../../html/dashboard_view.html'; // 🔧 Modify this path if the HTML file is moved
?>
