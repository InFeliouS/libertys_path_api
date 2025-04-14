<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/login.html"); 
    exit();
}

// Get teacher username from session
$teacher_username = $_SESSION['teacher_username'];

// Include the dashboard HTML view
include '../../html/dashboard_view.html';
?>