<?php
session_start();

// Only Manager can access
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager'){
    header("Location: ../auth/staff_login.php");
    exit();
}

require_once '../../config.php';

// Check if staff_id is provided
if (isset($_GET['staff_id'])) {
    $staff_id = intval($_GET['staff_id']); // sanitize input

    // Delete only if status is Pending
    $sql = "DELETE FROM staff_registration WHERE staff_id=$staff_id AND status='Pending'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg'] = "Staff request rejected successfully.";
    } else {
        $_SESSION['msg'] = "Error rejecting staff: " . mysqli_error($conn);
    }
}

mysqli_close($conn);

// Redirect back to manage staff page
header("Location: ../../frontend/manage_staff.php");
exit();
?>
