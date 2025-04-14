<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: /libertys_path_api/login"); // Redirect to login page without .html extension
exit();
?>
