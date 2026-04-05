<?php
session_start();
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager'){
    header("Location: ../auth/staff_login.php");
    exit();
}

require_once '../../config.php';

// Get staff_id from URL
$staff_id = intval($_GET['staff_id']);

// Update status to Approved
mysqli_query($conn, "UPDATE staff_registration SET status='Approved' WHERE staff_id=$staff_id");

mysqli_close($conn);

// Redirect back to manager page
header("Location: ../../frontend/manage_staff.php");
exit();
?>
