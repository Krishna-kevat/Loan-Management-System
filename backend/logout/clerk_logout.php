<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to clerk login page
header("Location: ../auth/staff_login.php");
exit();
?>
