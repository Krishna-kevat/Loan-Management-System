<?php
session_start(); // Resume the session

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: customer_login.html");
exit();
?>
