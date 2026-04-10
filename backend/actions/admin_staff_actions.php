<?php
session_start();
require_once '../config.php';

// 🔒 Ensure only Admin can access
if (!isset($_SESSION['super_admin_id']) || $_SESSION['role'] !== 'Super Admin') {
    die("Unauthorized access.");
}

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Default values for admin-created staff
    $status = 'Approved'; 
    $joining_date = date('Y-m-d');

    $sql = "INSERT INTO staff_registration 
            (fullname, email, password, phone, address, staff_id, role, status, joining_date) 
            VALUES 
            ('$fullname', '$email', '$password', '$phone', '$address', '$staff_id', '$role', '$status', '$joining_date')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/admin_manage_staff.php?msg=Staff added successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'delete') {
    $staff_id = mysqli_real_escape_string($conn, $_GET['staff_id']);
    $sql = "DELETE FROM staff_registration WHERE staff_id = '$staff_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/admin_manage_staff.php?msg=Staff deleted successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

elseif ($action === 'edit') {
    $original_id = mysqli_real_escape_string($conn, $_POST['original_staff_id']);
    $fullname    = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $staff_id    = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $role        = mysqli_real_escape_string($conn, $_POST['role']);
    
    $sql = "UPDATE staff_registration 
            SET fullname = '$fullname', 
                email = '$email', 
                staff_id = '$staff_id', 
                role = '$role' 
            WHERE staff_id = '$original_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/admin_manage_staff.php?msg=Staff updated successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}


elseif ($action === 'reset_pwd') {
    $staff_id = mysqli_real_escape_string($conn, $_POST['target_staff_id']);
    $new_pwd  = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $sql = "UPDATE staff_registration SET password = '$new_pwd' WHERE staff_id = '$staff_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../../frontend/admin_manage_staff.php?msg=Password reset successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
