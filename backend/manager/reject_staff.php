<?php
session_start();

// Only Manager can access
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'Manager'){
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","krishna_loan_management");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

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
header("Location: manage_staff.php");
exit();
?>
