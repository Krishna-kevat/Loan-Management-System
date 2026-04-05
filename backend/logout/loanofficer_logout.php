<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to staff login page
header("Location: ../../frontend/staff_login.html");
exit();
?>
