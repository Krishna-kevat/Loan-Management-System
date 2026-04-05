<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to staff login page
header("Location: staff_login.php");
exit();
?>
