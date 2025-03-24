<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: ../../html/login.html"); // Redirect to login page
exit();
?>
